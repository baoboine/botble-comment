<template>
    <div class="bb-comment-header" v-if="data.attrs">
        <div class="bb-comment-header-top d-flex justify-content-between">
            <strong>{{ data.attrs.count_all }} Comments</strong>

            <div class="btn-group">
                <button type="button" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @click="() => !isLogged && openLoginForm()">
                    <i class="fas fa-comment"></i>
                    {{ this.isLogged ? this.userData.name : 'Login' }}
                    <i class="fas fa-sort-down" v-if="this.isLogged"></i>
                </button>
                <div class="dropdown-menu" v-show="this.isLogged">
                    <a class="dropdown-item" href="javascript:" @click="logout">Logout</a>
                </div>
            </div>
        </div>


        <div class="bb-comment-header-bottom d-flex justify-content-between">
            <button class="btn btn-sm p-0 recommend-btn">
                <i class="far fa-heart"></i> Recommend
            </button>

            <div class="btn-group">
                <button type="button" class="btn btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Sort By {{ data.sort }}
                    <i class="fas fa-sort-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" v-bind:class="data.sort === 'newest' && 'active'" href="#" @click="() => onChangeSort('newest')">Newest</a>
                    <a class="dropdown-item" v-bind:class="data.sort === 'best' && 'active'" href="#" @click="() => onChangeSort('best')">Best</a>
                    <a class="dropdown-item" v-bind:class="data.sort === 'oldest' && 'active'" href="#" @click="() => onChangeSort('oldest')">Oldest</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Http from '../../service/http';
import Ls from '../../service/Ls';

export default {
    name: "Header",
    methods: {
        logout() {
            Http.post('/api/v1/logout').then(res => {
                Ls.remove('auth.token');
                this.getUser();
            });
        }
    },
    computed: {
        isLogged() {
            return this.data.userData;
        },
        userData() {
            return this.data.userData ?? {}
        }
    },
    inject: ['data', 'getUser', 'openLoginForm', 'onChangeSort']
}
</script>
