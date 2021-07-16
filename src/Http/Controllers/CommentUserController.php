<?php

namespace Botble\Comment\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Comment\Http\Requests\CommentUserRequest;
use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Comment\Tables\CommentUserTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Comment\Forms\CommentUserForm;
use Botble\Base\Forms\FormBuilder;

class CommentUserController extends BaseController
{
    /**
     * @var CommentUserInterface
     */
    protected $commentUserRepository;

    /**
     * @param CommentUserInterface $commentUserRepository
     */
    public function __construct(CommentUserInterface $commentUserRepository)
    {
        $this->commentUserRepository = $commentUserRepository;
    }

    /**
     * @param CommentUserTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(CommentUserTable $table)
    {
        page_title()->setTitle(trans('plugins/comment::comment-user.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/comment::comment-user.create'));

        return $formBuilder->create(CommentUserForm::class)->renderForm();
    }

    /**
     * @param CommentUserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CommentUserRequest $request, BaseHttpResponse $response)
    {
        $commentUser = $this->commentUserRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(COMMENT_USER_MODULE_SCREEN_NAME, $request, $commentUser));

        return $response
            ->setPreviousUrl(route('comment-user.index'))
            ->setNextUrl(route('comment-user.edit', $commentUser->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $commentUser = $this->commentUserRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $commentUser));

        page_title()->setTitle(trans('plugins/comment::comment-user.edit') . ' "' . $commentUser->name . '"');

        return $formBuilder->create(CommentUserForm::class, ['model' => $commentUser])->renderForm();
    }

    /**
     * @param int $id
     * @param CommentUserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CommentUserRequest $request, BaseHttpResponse $response)
    {
        $commentUser = $this->commentUserRepository->findOrFail($id);

        $commentUser->fill($request->input());

        $this->commentUserRepository->createOrUpdate($commentUser);

        event(new UpdatedContentEvent(COMMENT_USER_MODULE_SCREEN_NAME, $request, $commentUser));

        return $response
            ->setPreviousUrl(route('comment-user.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $commentUser = $this->commentUserRepository->findOrFail($id);

            $this->commentUserRepository->delete($commentUser);

            event(new DeletedContentEvent(COMMENT_USER_MODULE_SCREEN_NAME, $request, $commentUser));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $commentUser = $this->commentUserRepository->findOrFail($id);
            $this->commentUserRepository->delete($commentUser);
            event(new DeletedContentEvent(COMMENT_USER_MODULE_SCREEN_NAME, $request, $commentUser));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
