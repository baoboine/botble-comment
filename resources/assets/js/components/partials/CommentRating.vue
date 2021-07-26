<template>
    <div class="bb-comment-rating d-flex align-items-center">
        <span class="mr-2 text-large">{{ rating > 0 ? __('You rated this') : __('Rate and comment') }}</span>
        <star-rating style="margin-top: -5px;" :rating="rating" />
    </div>
</template>

<script>
import StarRating from './StarRating';
export default {
    name: "CommentRating",
    components: {
        StarRating
    },
    props: {
        rating: {
            type: Number,
            default: 0,
        }
    }
}
</script>

<style scoped>

</style>
