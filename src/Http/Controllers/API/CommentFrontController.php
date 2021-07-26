<?php


namespace Botble\Comment\Http\Controllers\API;


use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Comment\Models\Comment;
use Botble\Comment\Repositories\Interfaces\CommentInterface;
use Botble\Comment\Repositories\Interfaces\CommentLikeInterface;
use Botble\Comment\Repositories\Interfaces\CommentRatingInterface;
use Botble\Comment\Repositories\Interfaces\CommentRecommendInterface;
use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Botble\Comment\Events\NewCommentEvent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Botble\Comment\Supports\CheckMemberCredentials;
use RvMedia;

class CommentFrontController extends BaseController
{
    /**
     * @var BaseHttpResponse
     */
    protected $response;

    /**
     * @var CommentInterface
     */
    protected $commentRepository;

    /**
     * @var CheckMemberCredentials
     */
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
    public function postComment(Request $request, CommentRatingInterface $commentRatingRepo): BaseHttpResponse
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

        $user = $request->user();

        $request->merge(array_merge(
            [
                'ip_address'    => $request->ip(),
                'user_id'       => $user->getAuthIdentifier(),
            ], $reference
        ));

        $comment = $this->commentRepository->storageComment($request->only([
            'ip_address',
            'user_id',
            'reference_id',
            'reference_type',
            'reference',
            'comment',
            'parent_id',
            'rating',
        ]));

        if (setting('plugin_comment_rating', true) && $comment) {
            $comment->rating = $commentRatingRepo->storageRating($reference, $user, $request->input('rating', 0));
            $comment->rated = $commentRatingRepo->getRatingOfArticle($reference, $user);
        }

        event(new NewCommentEvent($comment, $user));

        return $this->response->setData($comment);
    }

    public function getComments(Request $request, CommentRecommendInterface $commentRecommendRepo, CommentRatingInterface $commentRatingRepo)
    {
        if (!($reference = $this->reference($request))) {
            return $this->response
                ->setError()
                ->setMessage(__('Invalid reference'));
        }
        $user = $this->memberCredentials->handle($request);
        $parentId = $request->input('up', 0);
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 5);
        $sort = $request->input('sort', 'newest');

        [$comments, $attrs] = $this->commentRepository->getComments($reference, $parentId, $page, $limit, $sort);

        $response = [
            'comments'      => $comments,
            'attrs'         => $attrs,
            'user'          => $user,
            'recommend'     => $commentRecommendRepo->getRecommendOfArticle($reference, $user),
        ];

        if (setting('plugin_comment_rating', true)) {
            $response['rating'] = $commentRatingRepo->getRatingOfArticle($reference, $user);
        }

        return $this->response
            ->setData($response);

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
     * @throws Exception
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
     * @param CommentLikeInterface $commentLikeRepo
     * @return BaseHttpResponse
     */
    public function likeComment(Request $request, CommentLikeInterface $commentLikeRepo)
    {
        $id = $request->input('id');
        $user = $request->user();

        $comment = $this->commentRepository->getFirstBy(compact('id'));

        $liked = $commentLikeRepo->likeThisComment($comment, $user);

        return $this->response
            ->setData(compact('liked'))
            ->setMessage($liked ? __('Like successfully') : __('Unlike successfully'));
    }


    public function changeAvatar(Request $request, CommentUserInterface $commentUserRepo, BaseHttpResponse $response)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return $response
                ->setError()
                ->setCode(422)
                ->setMessage(__('Data invalid!') . ' ' . implode(' ', $validator->errors()->all()) . '.');
        }

        try {

            $file = RvMedia::handleUpload($request->file('photo'), 0, 'members');
            if (Arr::get($file, 'error') !== true) {
                $commentUserRepo->createOrUpdate(['avatar_id' => $file['data']->id],
                    ['id' => $request->user()->getKey()]);
            }

            return $response
                ->setMessage(__('Update avatar successfully!'));

        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param CommentRecommendInterface $commentRecommendRepo
     * @return BaseHttpResponse
     */
    public function recommend(Request $request, CommentRecommendInterface $commentRecommendRepo): BaseHttpResponse
    {
        $reference = $this->reference($request);
        $user = $request->user();

        if ($reference) {
            $params = array_merge(
                Arr::only($reference, ['reference_type', 'reference_id']), ['user_id' => $user->id]
            );
            $recommend = $commentRecommendRepo->getFirstBy($params);

            if (!$recommend) {
                $commentRecommendRepo->createOrUpdate($params);
            } else {
                $recommend->delete();
            }

            return $this->response
                ->setData($recommend);
        }

        return $this->response
            ->setError();
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    protected function reference(Request $request)
    {
        try {
            $reference = json_decode(base64_decode($request->input('reference')), true);

            if (isset($reference['author']) && !empty($reference['author'])) {
                Comment::$author = app($reference['author']['type'])->where(['id' => $reference['author']['id']])->first();
            }

            return $reference;
        } catch (Exception $e) {
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
