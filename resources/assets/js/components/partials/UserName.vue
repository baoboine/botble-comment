<template>
    <div class="bb-comment-item-user_name" @mousemove="hovered = true" @mouseleave="hovered = false">
        <a href="javascript:"><strong>{{ user ? user.name : 'Guest' }}</strong></a>

        <div class="bb-comment-item-user_name-info" v-if="hovered">
            <div class="row">
                <div class="col-auto">
                    <img :src="user.avatar_url" :alt="user.name"/>
                </div>
                <div class="col">
                    <h5 class="mb-2">{{ user.name }}</h5>
                    <p>{{ __('Join date') }}: {{ this.parseDate() }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "UserName",
    data() {
        return {
            hovered: false,
        }
    },
    methods: {
        parseDate() {
            const date = new Date(this.user.created_at);
            return `${date.getDate()}/${date.getMonth() > 10 ? date.getMonth() : '0' + date.getMonth()}/${date.getFullYear()}`
        }
    },
    props: {
        user: {
            type: Object,
            default: {}
        }
    }
}
</script>
