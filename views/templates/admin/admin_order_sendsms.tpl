<form id="sendsms_order_form" class="defaultForm form-horizontal" method="post" novalidate="">
    <div id="formSendSmsPanel" class="card mt-2">
        <div class="card-header">
            <h3 class="card-header-title">
                {l s='Send SMS' mod='pssendsms'}
            </h3>
        </div>
        {if isset($sendsms_msg)}
        <div class="alert {if ! $sendsms_error}alert-success{else}alert-danger{/if}">
            {$sendsms_msg}
        </div>
        {/if}
        <div class="card-body">
            <div class="form-group">
                <label class="control-label required">
                    {l s='Phone number' mod='pssendsms'}
                </label>
                <div>
                    <input class="form-control" type="text" name="sendsms_phone" id="sendsms_phone" value="" class="" size="40" required="required">
                </div>
            </div>
            <div class="form-group type-checkbox row">
                <div class="col-sm">
                    <div class="checkbox">
                        <div class="md-checkbox md-checkbox-inline">
                        <label>
                            <input type="checkbox" name="sendsms_url" id="sendsms_url" value="on" class="" size="10">
                            <i class="md-checkbox-control">
                                {l s='Short url? (Please use only urls that start with https:// or http://)' mod='pssendsms'}
                            </i>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group type-checkbox row">
                <div class="col-sm">
                    <div class="checkbox">
                        <div class="md-checkbox md-checkbox-inline">
                        <label>
                            <input type="checkbox" name="sendsms_gdpr" id="sendsms_gdpr" value="on" class="" size="10">
                            <i class="md-checkbox-control">
                                {l s='Add an unsubscribe link? (You must specify {gdpr} key message. {gdpr} key will be replaced automaticaly with confirmation unique confirmation link.)' mod='pssendsms'}
                            </i>
                            </label>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label required">
                    {l s='Message' mod='pssendsms'}
                </label>
                <div>
                    <textarea name="sendsms_message" id="sendsms_message" class="form-control"></textarea>
                    <p>{l s='160 remaining characters' mod='pssendsms'}</p>
                    <script type="text/javascript">
                        var ps_sendsms_content = document.getElementById('sendsms_message');
                        ps_sendsms_content.onkeyup = function() {
                            var text_length = this.value.length;
                            var text_remaining = 160 - text_length;
                            this.nextElementSibling.innerHTML = text_remaining + '{l s=' remaining characters' mod='pssendsms'}';
                        }
                    </script>
                </div>
            </div>
            <div class="text-center">
            <button type="submit" value="1" id="sendsms_test_form_submit_btn" name="submitsendsms_order" class="btn btn-primary">
                <i class="process-icon-save"></i> {l s='Send' mod='pssendsms'}
            </button>
        </div>
        </div>
    </div>
</form>
