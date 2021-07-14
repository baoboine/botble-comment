<template>
    <div class="bb-avatar">
        <img src="../images/avatar.png" alt="avatar" class="img-circle" />
    </div>
</template>

<script>
export default {
    name: "Avatar",
    props: {
        user: {
            type: Object,
        }
    }
}
</script>
