<template>
    <modal :show.sync="show" :on-close="onClose" :loading="isLoading">
        <div slot="header">
            <h3>{{ title }}</h3>
        </div>

        <div class="mt-4 text-center" slot="body">
            <h6>{{ message }}</h6>
        </div>

        <div class="mt-4 d-flex justify-content-center" slot="footer">
            <button class="btn btn-secondary btn-lg" @click="() => onOK(false)">{{ __('Cancel') }}</button>
            <button class="btn btn-warning btn-lg" @click="() => onOK(true)">{{ __('OK') }}</button>
        </div>
    </modal>
</template>

<script>
import Modal from './Modal';
export default {
    name: 'ConfirmDialog',
    components: {
        Modal,
    },
    data() {
        return {
            show: false,
        }
    },
    mounted() {
        this.show = true;
    },
    props: {
        isLoading: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: 'Alert',
        },
        message: {
            type: String,
            default: 'Are you sure?',
        },
        onDone: {
            type: Function,
            default: () => null
        },
        onClose: {
            type: Function,
            default: () => null
        },
    },
    methods: {
        onOK(ok = false) {
            this.show = false;
            this.onDone(ok);
            setTimeout(() => {
                this.onClose();
            }, 500)
        }
    }
}
</script>

<style scoped>

</style>
