<template>
    <transition name="bb-dialog">
        <div class="bb-dialog-mask" v-show="show">
            <div class="bb-dialog-wrapper">
                <form class="bb-dialog-container" @submit.prevent="onSubmit">
                    <div class="bb-dialog-header">
                        <a class="bb-dialog-close" href="javascript:" @click="onClose">
                            <i class="fas fa-times"></i>
                        </a>
                        <slot name="header"> Default Header </slot>
                    </div>
                    <div class="bb-dialog-body">
                        <slot name="error"></slot>
                        <slot name="body"> Default Body </slot>
                    </div>
                    <div class="bb-dialog-footer">
                        <slot name="footer">
                            <button class="bb-dialog-default-button btn btn-primary" @click="onClose">OK</button>
                        </slot>
                    </div>

                    <div class="bb-dialog-loading" v-if="loading">
                        <div class="bb-loading mini"></div>
                    </div>
                </form>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'Modal',
    props:{
        show:{
            type: Boolean,
            required: true,
            twoWay: true
        },
        onClose: {
            type: Function,
            required: true
        },
        loading: {
            type: Boolean,
            default: false,
        },
        onSubmit: {
            type: Function,
            default: (e) => {
                e.preventDefault();
            }
        }
    },
    watch: {
        show() {
            if (this.show) {
                document.body.insertBefore(this.$el, document.body.firstChild)
                document.body.classList.add('bb-dialog-shown');
            } else {
                document.body.classList.remove('bb-dialog-shown');
            }
        }
    },
    beforeDestroy() {
        document.body.classList.remove('bb-dialog-shown');
    },
    mounted() {

    }
}
</script>
