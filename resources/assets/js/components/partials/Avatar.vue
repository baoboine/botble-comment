<template>
    <div class="bb-avatar text-center">
        <img :src="user && user.avatar_url ? user.avatar_url : '/vendor/core/plugins/comment/images/avatar.png'" alt="avatar" class="img-circle h-100" />

        <div class="bb-avatar-upload w-100 h-100" v-if="data.userData && user.id === data.userData.id" @click="uploadAvatar">
            <i class="fas fa-upload"></i>
        </div>
    </div>
</template>

<script>
import Http from '../../service/http';

export default {
    name: 'Avatar',
    props: {
        user: {
            type: Object,
        }
    },
    methods: {
        uploadAvatar() {
            let $file = $('<input type="file" accept="image/*" />');
            $file.click();

            $file.on('change', e => {

                this.setSoftLoading(true)

                const formData = new FormData();
                formData.append('photo', e.target.files[0]);

                Http.post(this.changeAvatarUrl, formData).then(() => {
                    window.location.reload();
                })
            })
        }
    },
    inject: ['data', 'changeAvatarUrl', 'setSoftLoading']
}
</script>
