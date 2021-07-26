<template>
    <div class="bb-comment-item" :class="{'is-sending': comment.isSending}">
        <div class="d-flex">
            <avatar :user="comment.user"></avatar>

            <div class="bb-comment-content w-100">
                <div class="bb-comment-content-user">
                    <user-name :user="comment.user"></user-name>
                    <span class="badge badge-warning" v-if="comment.isAuthor">{{ __('Author') }}</span>
                    <span class="px-1">•</span>
                    <span class="time">{{ !comment.isSending ? comment.time : 'sending...' }}</span>
                    <span class="px-1" v-if="comment.user.rating && comment.user.rating.rating">•</span>
                    <div class="d-inline-block" v-if="rated > 0">
                        <star-rating
                            :rating="rated"
                            :read-only="true"
                            :animate="false"
                            :star-size="15"
                        />
                    </div>
                </div>

                <div v-show="!showEdit">
                    <p v-html="linkify(comment.comment)"></p>

                    <div class="bb-comment-content-actions d-flex flex-wrap align-center">
                        <a class="reply" @click="replyIt" href="javascript:">{{ __('Reply') }}</a>
                        <span>•</span>
                        <a class="bb-like" :class="{'ok': comment.liked}" href="javascript:" @click="onLike">

                        </a>
                        <span>{{ comment.like_count }}</span>
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


                <a class="load-more-replies" href="javascript:" @click="onLoadMore" v-if="comments.length && attrs.current_page < attrs.last_page">{{ __('Load more replies') }} ({{ attrs.total - comments.length }}) <i class="fas fa-chevron-down"></i></a>

                <div v-if="comments.length" class="mt-3">
                    <comment-item v-for="(item, index) in comments" :key="item.id" :comment="item" :on-delete-item="() => onDeletedItem(index)"  />
                </div>
            </div>
        </div>

        <div class="bb-comment-item-actions" v-if="data.userData && parseInt(comment.user_id) === parseInt(data.userData.id)">
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
import UserName from "./UserName";
import StarRating from './StarRating';
import Http from '../../service/http';

export default {
    name: 'CommentItem',
    data() {
        return {
            showReply: false,
            comments: [],
            attrs: {},
            showEdit: false,
        }
    },
    components: {
        Avatar,
        CommentBox,
        UserName,
        StarRating,
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
            this.comments = rep.data.reverse();
            this.attrs = {
                total: rep.total,
                last_page: rep.last_page,
                current_page: rep.current_page,
                per_page: rep.per_page,
            }
        }
    },
    computed: {
        rated() {
            const rating = this.data.rating,
                comment = this.comment;

            if (
                rating &&
                comment &&
                rating.data[comment.user.id]
            ) {
                return rating.data[comment.user.id]
            }

            return 0;
        }
    },
    methods: {
        replyIt() {
            this.showReply = true;
        },
        onPostCommentSuccess(comment, isSending = false, fillIndex = null) {
            if (fillIndex === null) {
                comment.replies = [];
                comment.user = this.data.userData;
                comment.like_count = 0;
                comment.isSending = isSending;

                return this.comments.unshift(comment);
            } else {
                if (fillIndex !== -1) {
                    this.showReply = false;
                    comment.isSending = false;
                    this.comments[0] = Object.assign(this.comments[0], comment);
                } else {
                    // failed
                    this.showReply = true;
                    this.comments.splice(0, 1);
                }
            }
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
            this.showConfirm(this.__('Confirm'), this.__('Are you sure that want to delete this comment?'), (ok) => {
                if (ok) {
                    this.setSoftLoading(true);
                    Http.delete(this.deleteUrl, {
                        params: {
                            id: this.comment.id,
                        }
                    }).then(res => {
                        const {data} = res;

                        this.setSoftLoading(false);

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
                this.comments = data.data.comments.reverse().concat(this.comments);
                this.attrs = data.data.attrs;
            })
        },
        onLike() {
            this.comment.liked = !this.comment.liked;

            this.comment.like_count += this.comment.liked ? 1 : -1;

            Http.post(this.likeUrl, {
                id: this.comment.id,
            }).then(() => {

            });
        }
    },
    inject: ['data', 'deleteUrl', 'showConfirm', 'updateCount', 'apiLoadComments', 'setSoftLoading', 'likeUrl']
}
</script>
