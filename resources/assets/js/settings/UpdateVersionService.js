class UpdateVersionService {

    constructor(element) {
        const apis = element.data('apis');
        this.$el = element;
        this.$buttonCheck = element.find('#check-version');

        this.checkLatestApi = apis.check;
        this.downloadApi = apis.download;

        this.initEvents();
    }

    initEvents() {
        this.$buttonCheck.on('click', this.check.bind(this));
    }

    check() {
        this.$buttonCheck
            .prop('disabled', true)
            .addClass('button-loading');

        if (!this.$buttonCheck.hasClass('has-new-version')) {
            this.callApi(this.checkLatestApi).then(res => {
                const data = res.data;

                if (data.has) { // has new updates

                    this.$buttonCheck.before(`
                        <div class="alert alert-warning">
                            Comment version <strong>${data.version}</strong> is now available. Would you like to download it now?
                        </div>
                    `)

                    this.$buttonCheck.attr('class', 'btn btn-primary')
                        .addClass('has-new-version')
                        .prop('disabled', false)
                        .html('<i class="fas fa-download"></i> Install Update');
                } else {
                    this.$buttonCheck.before(`
                        <div class="alert alert-info">
                            Comment Plugin is up to update.
                        </div>
                    `)
                    this.$buttonCheck.removeClass('button-loading');
                }
            })
        } else {
            let $alert = this.$buttonCheck.prev('.alert');

            if ($alert.length) {
                $alert.slideUp(500)
            }

            this.callApi(this.downloadApi, {}, 'POST').then(res => {
                console.log('res', res)
                this.$buttonCheck
                    .prop('disabled', false)
                    .removeClass('button-loading');
                if (!res.error && res.data.ok) {
                    $alert.replaceWith(`
                        <div class="alert alert-success">
                            Update plugin successfully! Press <a onclick="window.location.reload()"><strong>Reload</strong></a> to finish
                        </div>
                    `).slideDown()
                } else {
                    $alert.replaceWith(`
                        <div class="alert alert-danger">
                            ${res.message ?? 'There are somethings wrong. Please try again'}
                        </div>
                    `).slideDown()
                }
            });
        }
    }


    callApi(url, data, method = 'GET') {
        return new Promise(resolve => {
            return $.ajax({
                url,
                data,
                method,
                success(res) {
                    resolve(res);
                },
                error(res) {
                    resolve({error: true, message: res?.responseJSON?.message});
                }
            })
        })
    }

}

export default UpdateVersionService;
