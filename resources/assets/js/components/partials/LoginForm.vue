<template>
    <modal :show.sync="show" :on-close="onClose" :on-submit="onSubmit" :loading="isLoading">
        <div slot="header">
            <h3>{{ !this.isRegistering ? __('Sign in to our platform') : __('Sign Up') }}</h3>
            <p class="text-gray">{{ __('Use your credentials to access your account.') }}</p>
        </div>

        <div v-if="errorMessages" slot="error">
            <div class="alert alert-danger" v-for="mess in errorMessages">
                <span>{{ mess }}</span>
            </div>
        </div>

        <div class="mt-4" slot="body" v-if="!isRegistering">
            <input class="hrv-checkbox d-none" checked="checked" name="remember" type="checkbox" value="1">

            <div class="form-group" key="email">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                    </div>
                    <input type="email" autofocus class="form-control form-control-lg" name="email" :placeholder="__('Enter email')" required="">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                    </div>
                    <input class="form-control form-control-lg" :placeholder="__('Password')" name="password" type="password" required="">
                </div>

            </div>
        </div>

        <div class="d-block d-sm-flex justify-content-between align-items-center mt-2" slot="footer" v-if="!isRegistering">
            <div>
                <a href="javascript:" @click="isRegistering = true" class="small text-right">{{ __('Create account') }}</a>
            </div>
            <div>
                <button class="btn btn-primary btn-block" type="submit">{{ __('Login') }}</button>
            </div>
        </div>

        <!--  Register -->

        <div class="mt-4" slot="body" v-if="isRegistering">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-user"></i></span>
                    </div>
                    <input type="text" autofocus="true" class="form-control form-control-lg" name="name" :placeholder="__('Enter name')" required="">
                </div>
            </div>
            <div class="form-group" key="email">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                    </div>
                    <input type="email" autofocus="true" class="form-control form-control-lg" name="email" :placeholder="__('Enter email')"  required="">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                    </div>
                    <input class="form-control form-control-lg" :placeholder="__('Password')" name="password" type="password" required="">
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                    </div>
                    <input class="form-control form-control-lg" :placeholder="__('Confirm Password')" name="password_confirmation" type="password" required="">
                </div>
            </div>

            <div v-html="captcha" />
        </div>

        <div slot="footer">
            <div class="d-block d-sm-flex justify-content-between align-items-center mt-2" v-if="isRegistering">
                <div>
                    <a href="javascript:" @click="isRegistering = false" class="small text-right">{{ __('Back to login') }}</a>
                </div>
                <div>
                    <button class="btn btn-primary" type="submit">{{ __('Sign Up') }}</button>
                </div>
            </div>
            <div v-if="loginWithGuards && loginWithGuards.length" class="bb-comment-login-with-guards">
                <hr>
                <div class="text-center">
                    <p>{{ __('Or login with') }}</p>
                </div>
                <div class="guards-container">
                    <div v-for="user in loginWithGuards" :key="user.key" @click="onLoginWithGuard(user)" class="item">
                        <div class="border rounded px-2 py-2">
                            <div class="content">
                                <img :src="user.avatar" :alt="user.title" class="avatar">
                                <span>{{ user.title }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
import Modal from './Modal';
import Http from '../../service/http';
import Ls from '../../service/Ls';

export default {
    name: 'LoginForm',
    data: () => {
        return {
            isLoading: false,
            isRegistering: false,
            errorMessages: null,
        }
    },
    components: {
        modal: Modal
    },
    props: {
        show:{
            type: Boolean,
            required: true,
            twoWay: true
        },
        onClose: {
            type: Function,
            required: true,
        },
        loginWithGuards: {
            type: Array,
            default: [],
        },
        onLoginWithGuard: {
            type: Function,
        }
    },
    watch: {
        isRegistering() {
            if (this.isRegistering && window.grecaptcha) {
                setTimeout(() => {
                    this.$el.querySelector('.g-recaptcha') && grecaptcha?.render(this.$el.querySelector('.g-recaptcha'));
                }, 500)
            }
        }
    },
    methods: {
        onSuccess({ data }) {
            this.isLoading = false;
            if (data.error) {
                this.setErrors(data.message);
                return;
            }
            if (!data.data.token) {
                this.setErrors({'token': ['Request is failed, please check again']});
                return;
            }

            this.setErrors(false);
            Ls.set('auth.token', data.data.token);
            this.onClose(true);
        },
        onError(res) {
            this.isLoading = false;
            const data = res.response?.data ?? false;
            if (data) {
                this.setErrors(data.errors);
            } else {
                this.setErrors({'token': ['Request is failed, please check again']});
            }
        },
        setErrors(messages) {
            if (!messages) {
                this.errorMessages = false;
                 return;
            }

            this.errorMessages = Object.keys(messages).map(key => {
                return messages[key][0];
            })
        },
        onSubmit(e) {
            this.isLoading = true;
            const formData = $(e.target).serializeData();

            if (formData.g?.recaptcha) {
                formData['g-recaptcha-response'] = formData.g.recaptcha.response;
            }

            Http.post(!this.isRegistering ? this.loginUrl : this.registerUrl, formData).then(this.onSuccess, this.onError);
        },
    },
    inject: ['loggedUser', 'loginUrl', 'registerUrl', 'captcha']
}
</script>
