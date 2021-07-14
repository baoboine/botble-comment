<template>
    <div class="bb-comment-item">
        <div class="d-flex">
            <avatar></avatar>

            <div class="bb-comment-content w-100">
                <div class="bb-comment-content-user">
                    <strong>{{ comment.user ? comment.user.name : "Guest" }}</strong>
                    <span class="badge badge-warning">Author</span>
                    <span class="px-2">•</span>
                    <span class="time">{{ comment.time }}</span>
                </div>

                <div v-show="!showEdit">
                    <p>
                        {{ comment.comment }}
                    </p>

                    <div class="bb-comment-content-actions d-flex flex-wrap align-center">
                        <a class="reply" @click="replyIt" href="javascript:">Reply</a>
                        <span>•</span>
                        <a class="like" href="javascript:">
                            <i class="fas fa-thumbs-up"></i>
                        </a>
                    </div>

                    <div class="mt-3 mb-4" v-if="showReply">
                        <comment-box :parent-id="comment.id" :on-success="onPostCommentSuccess" auto-focus="true" />
                    </div>
                </div>

                <!-- Edit form -->
                <comment-box
                    auto-focus="true"
                    v-if="showEdit"
                    is-edit="true"
                    :default-value="comment.comment"
                    :on-success="onEditCommentSuccess"
                    :on-cancel="onCancel"
                    :parent-id="comment.parent_id"
                    :comment-id="comment.id"
                />


                <a class="load-more-replies" href="javascript:" @click="onLoadMore" v-if="comments.length && attrs.current_page < attrs.last_page">Load more replies ({{ attrs.total - comments.length }}) <i class="fas fa-chevron-down"></i></a>

                <div v-if="comments.length" class="mt-3">
                    <comment-item v-for="(item, index) in comments" :key="item.id" :comment="item" :on-delete-item="() => onDeletedItem(index)"  />
                </div>
            </div>
        </div>

        <div class="bb-comment-item-actions">
            <a href="javascript:" @click="showEdit = true">
                <i class="fas fa-edit"></i>
            </a>
            <a href="javascript:" class="ml-2" @click="onDelete">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    </div>
</template>

<script>
import Avatar from "./Avatar";
import CommentBox from "./CommentBox";
import Http from '../../service/http';

export default {
    name: "CommentItem",
    data() {
        return {
            showReply: false,
            comments: [],
            attrs: {},
            showEdit: false,
        }
    },
    components: {
        avatar: Avatar,
        'comment-box': CommentBox,
    },
    props: {
        comment: {
            type: Object,
            required: true,
        },
        onDeleteItem: {
            type: Function,
            required: true,
            default: () => null,
        }
    },
    mounted() {
        const rep = this.comment.rep;

        if (rep && rep.data) {
            this.comments = rep.data;
            this.attrs = {
                total: rep.total,
                last_page: rep.last_page,
                current_page: rep.current_page,
                per_page: rep.per_page,
            }
        }
    },
    methods: {
        replyIt() {
            this.showReply = true;
        },
        onPostCommentSuccess(comment) {
            comment.replies = [];
            comment.user = this.data.userData;

            this.showReply = false;
            this.comments.unshift(comment);
        },
        onCancel() {
            this.showEdit = false;
            this.showReply = false;
        },
        onEditCommentSuccess(comment) {
            this.showEdit = false;
            this.comment = comment;
        },
        onDelete() {
            this.showConfirm('Confirm', 'Are you sure that want to delete this comment?', (ok) => {
                if (ok) {
                    Http.delete(this.deleteUrl, {
                        params: {
                            id: this.comment.id,
                        }
                    }).then(res => {
                        const {data} = res;

                        if (!data.error) {
                            this.updateCount(false);
                            this.onDeleteItem();
                        }
                    })
                }
            })
        },
        onDeletedItem(index) {
            this.comments.splice(index, 1)
        },
        onLoadMore() {
            this.apiLoadComments({
                up: this.comment.id,
                page: this.attrs.current_page + 1,
                limit: this.attrs.per_page,
            }, data => {
                this.comments = data.data.comments.concat(this.comments);
                this.attrs = data.data.attrs;
            })
        }
    },
    inject: ['data', 'deleteUrl', 'showConfirm', 'updateCount', 'apiLoadComments']
}
</script>
