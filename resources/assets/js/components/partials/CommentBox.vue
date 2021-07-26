<template>
    <div class="bb-comment-box d-flex" :class="{'has-rating': hasRating}">
        <avatar v-if="!isEdit" :user="data.userData"></avatar>
        <form class="bb-textarea" @submit="this.postComment">
            <comment-rating v-if="hasRating" :rating="data.rating ? data.rating.rated : 0" />

            <textarea class="form-control" rows="1" name="comment" placeholder="Share your thoughts about that" @change="onChange" :value="value" />
            <div class="bb-textarea-error alert alert-danger m-0" v-if="error">
                <span>{{ error }}</span>
            </div>
            <div class="bb-textarea-footer">
                <div class="bb-textarea-footer-left">

                </div>

                <div class="bb-textarea-footer-right" v-if="!isEdit">
                    <button type="submit" v-if="data.userData" :class="'post-btn' + (isSending ? ' button-loading' : '')">{{ __('Post as') }} {{ data.userData.name }}</button>
                    <button type="submit" v-if="!data.userData" class="post-btn post-none">{{ __('Login to Post') }}</button>
                </div>

                <div class="bb-textarea-footer-right" v-if="isEdit">
                    <button type="button" class="post-btn cancel-btn" @click="onCancel">{{ __('Cancel') }}</button>
                    <button type="submit" v-if="data.userData" :class="'post-btn' + (isSending ? ' button-loading' : '')">{{ __('Update') }}</button>
                </div>
            </div>

            <input type="hidden" name="reference" :value="reference" />
            <input type="hidden" name="parent_id" :value="parentId" />
            <input type="hidden" name="comment_id" :value="commentId" v-if="isEdit" />
        </form>
    </div>
</template>

<script>
import Avatar from './Avatar';
import CommentRating from "./CommentRating";
import { setResizeListeners } from '../helpers';
import Http from '../../service/http';

export default {
    name: 'CommentBox',
    components: {
        Avatar,
        CommentRating,
    },
    data: () => {
        return {
            isSending: false,
            error: false,
            value: '',
        }
    },
    methods: {
        onChange(e) {
            this.value = e.target.value;
        },
        postComment(e) {
            e.preventDefault();
            if (!this.data.userData) {
                this.openLoginForm();
            } else {
                const formData = $(e.target).serializeData()
                const index = this.onSuccess(formData, true)
                this.isSending = true;

                Http.post(this.postUrl, formData)
                    .then(({ data }) => {
                        this.isSending = false;
                        if (!data.error) {
                            this.value = '';
                            const textarea = this.$el.querySelector('textarea');
                            this.onSuccess(data.data, false, index);
                            this.error = false;
                            textarea.value = '';
                            textarea.classList.remove('focused')
                            textarea.style.height = 'auto';
                            this.updateCount();
                        } else {
                            this.onSuccess(null, false, -1);
                            this.error = data.message[Object.keys(data.message)[0]][0]
                        }
                    }, error => {
                        this.onSuccess(null, false, -1);
                        this.isSending = false;
                        this.error = error.response?.statusText ?? error.message;
                    })
            }
        }
    },
    props: {
        parentId: {
            type: Number,
            default: 0,
        },
        commentId: {
            type: Number,
            default: 0,
        },
        onSuccess: {
            type: Function,
            default: () => null
        },
        onCancel: {
            type: Function,
            default: () => null
        },
        autoFocus: {
            type: Boolean,
            default: false,
        },
        isEdit: {
            type: Boolean,
            default: false,
        },
        defaultValue: {
            type: String,
            default: '',
        },
        hasRating: {
            type: Boolean,
            default: false,
        }
    },
    mounted() {
        setResizeListeners(this.$el, 'textarea');

        if (this.autoFocus) {
            this.$el.querySelector('textarea').focus()
        }

        if (this.defaultValue) {
            this.value = this.defaultValue;
        }
    },
    inject: ['getUser', 'data', 'reference', 'postUrl', 'updateCount', 'openLoginForm']
}
</script>
