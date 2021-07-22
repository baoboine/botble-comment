<template>
    <div class="bb-comment-header" v-if="data.attrs">
        <div class="bb-comment-header-top d-flex justify-content-between">
            <strong>{{ data.attrs.count_all }} {{ __('Comments') }}</strong>

            <div class="btn-group">
                <button type="button" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @click="() => !isLogged && openLoginForm()">
                    <i class="fas fa-comment"></i>
                    {{ this.isLogged ? this.userData.name : 'Login' }}
                    <i class="fas fa-sort-down" v-if="this.isLogged"></i>
                </button>
                <div class="dropdown-menu" v-show="this.isLogged">
                    <a class="dropdown-item" href="javascript:" @click="logout">{{ __('Logout') }}</a>
                </div>
            </div>
        </div>


        <div class="bb-comment-header-bottom d-flex justify-content-between">
            <button class="btn btn-sm p-0 recommend-btn bb-heart" :class="{'font-weight-bold': isRecommended}" @click="onRecommend">
                <span data-text="❤" :class="{'active': isRecommended}">❤</span> {{ !isRecommended ? __('Recommend') : __('Recommended') }}
                <span class="badge badge-secondary" v-if="countRecommend > 0">{{ countRecommend }}</span>
            </button>

            <div class="btn-group">
                <button type="button" class="btn btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('Sort By') }} {{ data.sort }}
                    <i class="fas fa-sort-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" v-bind:class="data.sort === 'newest' && 'active'" href="javascript:" @click="() => onChangeSort('newest')">{{ __('Newest') }}</a>
                    <a class="dropdown-item" v-bind:class="data.sort === 'best' && 'active'" href="javascript:" @click="() => onChangeSort('best')">{{ __('Best') }}</a>
                    <a class="dropdown-item" v-bind:class="data.sort === 'oldest' && 'active'" href="javascript:" @click="() => onChangeSort('oldest')">{{ __('Oldest') }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Http from '../../service/http';
import Ls from '../../service/Ls';

export default {
    name: 'Header',
    data: () => {
        return {
            isRecommended: false,
            countRecommend: 0,
        }
    },
    methods: {
        logout() {
            Http.post(this.logoutUrl).then(() => {
                Ls.remove('auth.token');
                this.getUser();
            });
        },
        async onRecommend() {
            const res = await Http.post(this.recommendUrl, { reference: this.reference });
            this.isRecommended = !res.data.data;
            this.countRecommend += this.isRecommended ? 1 : -1;
        }
    },
    props: {
        recommend: {
            type: Object,
        }
    },
    computed: {
        isLogged() {
            return this.data.userData;
        },
        userData() {
            return this.data.userData ?? {}
        },
    },
    watch: {
        recommend() {
            if (typeof this.recommend.isRecommended !== 'undefined') {
                this.isRecommended = this.recommend.isRecommended;
                this.countRecommend = this.recommend.count;
            }
        }
    },
    inject: ['reference', 'data', 'getUser', 'openLoginForm', 'onChangeSort', 'logoutUrl', 'recommendUrl']
}
</script>
