<?php

namespace Botble\Comment\Tables;

use Auth;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Comment\Repositories\Interfaces\CommentInterface;
use Botble\Setting\Supports\SettingStore;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Comment\Models\Comment;
use Html;

class CommentTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * CommentTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param CommentInterface $commentRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CommentInterface $commentRepository)
    {
        $this->repository = $commentRepository;
        $this->setOption('id', 'plugins-comment-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['comment.edit', 'comment.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('comment', function ($item) {
                return $item->comment;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return $item->time;
            })
            ->editColumn('reference', function($item) {
                return Html::link($item->reference->url . '#bb-comment', $item->reference->name, ['target' => '_blank']);
            })
            ->editColumn('user', function($item) {
                return $item->user ? $item->user->name : 'Guest';
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        $this->storageLatestViewed();

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations(false, 'comment.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $query = $this->repository->getModel()
            ->with(['reference'])
            ->select(['*']);

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'comment' => [
                'title' => trans('plugins/comment::comment.name'),
                'class' => 'text-left',
            ],
            'user' => [
                'title' => trans('plugins/comment::comment.user'),
                'class' => 'text-left',
            ],
            'reference' => [
                'title' => trans('plugins/comment::comment.article'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('comment.create'), 'comment.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Comment::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('comment.deletes'), 'comment.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'comments.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'comments.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'comments.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }

    /**
     * storage latest viewed comment id
     */
    protected function storageLatestViewed()
    {
        if ((int)request()->input('start', -1) === 0) {
            $latestId = $this->repository->getModel()->latest()->first();
            if ($latestId && (int)setting('admin-comment_latest_viewed_id', 0) !== $latestId) {
                app(SettingStore::class)->set('admin-comment_latest_viewed_id', $latestId->id)->save();
            }
        }
    }
}
