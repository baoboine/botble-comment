<template>
    <div class="bb-comment">
        <comment-header />
        <comment-box :on-success="onPostCommentSuccess" />
        <div class="bb-loading" v-if="isLoading"></div>
        <div class="bb-comment-list" v-if="!isLoading">
            <comment-item v-for="(comment, index) in comments" :key="comment.id" :comment="comment" :on-delete-item="() => onDeletedItem(index)" />

            <div class="bb-comment-list-empty text-center" v-if="!comments.length">
                <p>{{ __('Become the first to comment') }}</p>
            </div>
        </div>

        <confirm-dialog v-if="confirmDialogData"
                        :title="confirmDialogData.title"
                        :message="confirmDialogData.message"
                        :on-done="confirmDialogData.onDone"
                        :on-close="() => this.confirmDialogData = null"
        />

        <login-form :show.sync="showLoginForm" :on-close="closeModalLogin"></login-form>

        <div class="bb-comment-footer d-flex justify-content-center" v-if="reactive.attrs">
            <button v-if="reactive.attrs.last_page > reactive.attrs.current_page" @click="loadMoreComments" :class="'btn btn-secondary' + (isLoadMore ? ' button-loading' : '')">{{ __('Load More') }}</button>
        </div>

        <div class="bb-comment-loading" v-if="isSoftLoading">
            <div class="bb-loading mini"></div>
        </div>
    </div>
</template>

<script>
import CommentBox from "./partials/CommentBox";
import CommentItem from "./partials/CommentItem";
import CommentHeader from "./partials/CommentHeader";
import ConfirmDialog from "./partials/ConfirmDialog";
import LoginForm from "./partials/LoginForm";
import Http from '../service/http';
import Ls from '../service/Ls';

window.http = Http;

export default {
    data: () => {
        return {
            isLoading: true,
            isLoadMore: false,
            error: false,
            comments: [],
            reactive: {
                userData: null,
                attrs: null,
                sort: 'newest',
            },
            confirmDialogData: null,
            showLoginForm: false,
            isSoftLoading: false,
        };
    },
    components: {
        CommentBox,
        CommentItem,
        CommentHeader,
        ConfirmDialog,
        LoginForm,

    },
    props: {
        reference: {
            type: String,
            required: true
        },
        url: {
            type: String,
            required: true
        },
        postUrl: {
            type: String,
            required: true
        },
        userUrl: {
            type: String,
            required: true,
        },
        deleteUrl: {
            type: String,
            required: true,
        },
        likeUrl: {
            type: String,
            required: true,
        },
        changeAvatarUrl: {
            type: String,
            required: true,
        },
        loggedUser: {
            type: Object,
        },
        checkCurrentUserApi: {
            type: String,
            required: true,
        },
    },
    methods: {
        getCurrentUserData() {
            if (Ls.get('auth.token')) {
                this.setSoftLoading(true);
                Http.post(this.userUrl).then(res => {
                    this.reactive.userData = res.data.data;
                    this.setSoftLoading(false)
                })
            } else {
                this.reactive.userData = null;
            }
        },
        apiLoadComments(params, cb) {
            Http.get(this.url, {
                params: {
                    reference: this.reference,
                    sort: this.reactive.sort,
                    ...params,
                }
            })
                .then(({ data }) => {
                    if (data.error) {
                        this.error = true;
                        return;
                    }

                    this.error = false;
                    cb(data);
                })
        },
        loadComments() {
            this.isLoading = true;
            this.apiLoadComments({

            }, data => {
                this.isLoading = false;
                this.comments = data.data.comments;
                this.reactive.userData = data.data.user;
                this.reactive.attrs = data.data.attrs;
            })
        },
        loadMoreComments() {
            if (this.url && this.reactive.attrs) {
                this.isLoadMore = true;
                this.apiLoadComments({
                    page: this.reactive.attrs.current_page + 1
                }, data => {
                    this.isLoadMore = false;
                    this.comments = this.comments.concat(data.data.comments);
                    this.reactive.attrs = data.data.attrs;
                })
            }
        },
        onPostCommentSuccess(comment) {
            comment.replies = [];
            comment.user = this.reactive.userData;

            this.comments.unshift(comment);
        },
        showConfirmDialog(title, message, onDone) {
            this.confirmDialogData = {
                title,
                message,
                onDone
            }
        },
        onDeletedItem(index) {
            this.comments.splice(index, 1)
        },
        updateCount(adding = true) {
            if (adding) {
                this.reactive.attrs.count_all += 1;
            } else {
                this.reactive.attrs.count_all -= 1;
            }
        },
        closeModalLogin(isSuccess = false) {
            this.showLoginForm = false;

            if (isSuccess) {
                this.getCurrentUserData();
            }
        },
        openLoginForm() {
            this.checkCurrentUser(() => {
                this.showLoginForm = true;
            });
        },
        onChangeSort(sort) {
            this.reactive.sort = sort;
            this.loadComments();
        },
        checkCurrentUser(cb) {
            this.setSoftLoading(true);
            Http.post(this.checkCurrentUserApi)
                .then(res => {
                    this.setSoftLoading(false);
                    if (res.data.error) {
                        cb();
                    } else {
                        Ls.set('auth.token', res.data.data.token);
                        this.getCurrentUserData();
                    }
                }, () => {
                    this.setSoftLoading(false);
                    cb();
                })
        },
        setSoftLoading(value = true) {
            this.isSoftLoading = value;
        }
    },
    mounted: function () {
        this.loadComments();
    },
    provide() {
        return {
            getUser: this.getCurrentUserData,
            data: this.reactive,
            loggedUser: this.loggedUser,
            reference: this.reference,
            postUrl: this.postUrl,
            deleteUrl: this.deleteUrl,
            likeUrl: this.likeUrl,
            showConfirm: this.showConfirmDialog,
            updateCount: this.updateCount,
            openLoginForm: this.openLoginForm,
            apiLoadComments: this.apiLoadComments,
            onChangeSort: this.onChangeSort,
            setSoftLoading: this.setSoftLoading,
            changeAvatarUrl: this.changeAvatarUrl,
        }
    }
}
</script>
