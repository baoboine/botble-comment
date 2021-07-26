<template>
    <div class="bb-comment-header" v-if="data.attrs">

        <div class="bb-comment-header-ratings text-center" v-if="hasRating">
            <span>{{ data.rating.count }} {{ __('Ratings') }}</span>
            <div class="d-block text-center">
                <star-rating :rating="data.rating.rating" :star-size="30" :animate="false" :read-only="true" style="display: inline-block" />
            </div>
        </div>

        <div class="bb-comment-header-top d-flex justify-content-between">
            <strong>{{ data.attrs.count_all }} {{ __('Comments') }}</strong>
            <dropdown
                @click="() => !isLogged && openLoginForm()"
                icon="fas fa-comment"
                :selected="{name: isLogged ? userData.name : 'Login'}"
                :options="isLogged && [
                    {name: __('Logout'), onClick: logout}
                ]"
                :no-select-mode="true"
            />
        </div>


        <div class="bb-comment-header-bottom d-flex justify-content-between">
            <button class="btn btn-sm p-0 recommend-btn bb-heart" :class="{'font-weight-bold': isRecommended}" @click="onRecommend">
                <span data-text="❤" :class="{'active': isRecommended}">❤</span> {{ !isRecommended ? __('Recommend') : __('Recommended') }}
                <span class="badge badge-secondary" v-if="countRecommend > 0">{{ countRecommend }}</span>
            </button>

            <dropdown
                :selectedValue="data.sort"
                v-on:updateOption="onChangeSort"
                :options="[
                    {name: __('Newest'), value: 'newest'},
                    {name: __('Best'), value: 'best'},
                    {name: __('Oldest'), value: 'oldest'}
                ]"
            />
        </div>
    </div>
</template>

<script>
import Http from '../../service/http';
import Ls from '../../service/Ls';
import Dropdown from "./Dropdown";
import StarRating from './StarRating';

export default {
    name: 'Header',
    components: {
        Dropdown,
        StarRating,
    },
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
        },
        hasRating: {
            type: Boolean,
            default: false,
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
