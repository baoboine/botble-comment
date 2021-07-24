<!-- Please don't remove this file, If you respect what I did -->
<template>
    <div class="bb-comment-footer">
        <h6><a href="https://github.com/baoboine/botble-comment" target="_blank">{{ text }}</a> </h6>
    </div>
</template>

<script>
export default {
    name: 'CommentFooter',
    props: {
        text: {
            type: String
        }
    }
}
</script>

<style scoped lang="scss">
    .bb-comment-footer {
        border-top: 1px solid #ccc;
        width: 100%;
        padding: 1rem 0;
        margin-top: 1rem;

        a {
            color: #ccc;
            font-size: 12px;
        }
    }
</style>
