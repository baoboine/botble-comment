<?php


namespace Botble\Comment\Http\Controllers\API;


use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Comment\Repositories\Interfaces\CommentInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Botble\Comment\Supports\CheckMemberCredentials;

class CommentFrontController extends BaseController
{
    protected $response;

    protected $commentRepository;

    protected $memberCredentials;

    public function __construct(BaseHttpResponse $response, CommentInterface $commentRepository, CheckMemberCredentials $memberCredentials)
    {
        $this->response = $response;
        $this->commentRepository = $commentRepository;
        $this->memberCredentials = $memberCredentials;
    }

    /**
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function postComment(Request $request): BaseHttpResponse
    {
        $validate = $this->validator($request->input());
        if ($validate->fails()) {
            return $this->response
                ->setMessage($validate->getMessageBag())
                ->setError(true);
        }

        if (!($reference = $this->reference($request))) {
            return $this->response
                ->setError()
                ->setMessage(__('Invalid reference'));
        }

        $request->merge(array_merge(
            [
                'ip_address'    => $request->ip(),
                'user_id'       => $request->user()->getAuthIdentifier(),
            ], $reference
        ));

        $comment = $this->commentRepository->storageComment($request->input());

        return $this->response->setData($comment);
    }

    public function getComments(Request $request)
    {
        if (!($reference = $this->reference($request))) {
            return $this->response
                ->setError()
                ->setMessage(__('Invalid reference'));
        }
        $user = null;
        $parentId = $request->input('up', 0);
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 5);
        $sort = $request->input('sort', 'newest');


        if ($token = $this->memberCredentials->handle()) {
            $user = $token->user($request);
        }

        list($comments, $attrs) = $this->commentRepository->getComments($reference, $parentId, $page, $limit, $sort);

        return $this->response
            ->setData([
                'comments'  => $comments,
                'attrs'     => $attrs,
                'user'      => $user,
            ]);

    }

    /**
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function userInfo(Request $request): BaseHttpResponse
    {
        return $this->response
            ->setData($request->user());
    }

    /**
     * @throws \Exception
     */
    public function deleteComment(Request $request)
    {
        $userId = $request->user()->getAuthIdentifier();
        $id = $request->input('id');

        if (!$id) {
            return $this->response
                ->setError()
                ->setMessage(__('Comment ID is required'));
        }

        $comment = $this->commentRepository->getFirstBy(compact('id'));

        if (!$comment || $comment->user_id !== $userId) {
            return $this->response
                ->setError()
                ->setMessage(__('You don\'t have permission with this comment'));
        }

        $this->commentRepository->delete($comment);

        return $this->response
            ->setMessage(__('Delete comment successfully'));
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    protected function reference(Request $request)
    {
        try {
            return json_decode(base64_decode($request->input('reference')), true);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'reference'         => 'required',
            'comment'           => 'required|min:5'
        ]);
    }
}
