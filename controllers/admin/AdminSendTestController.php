<?php
class AdminSendTestController extends ModuleAdminController
{
    protected $index;
    private $csrf;

    public function __construct()
    {
        parent::__construct();
        require_once dirname(__FILE__) . '/../../csrf.class.php';
        $this->csrf = new Csrf();
        $this->table = 'sendsms_test';
        $this->bootstrap = true;
        $this->meta_title = $this->module->l('Send a test SMS');
        $this->display = 'add';

        $this->index = count($this->_conf) + 1;
        $this->_conf[$this->index] = $this->module->l('The message was sent');
    }

    public function initContent()
    {
        $this->show_page_header_toolbar = true;
        $this->page_header_toolbar_btn['back'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name,
            'desc' => $this->module->l('Back to module'),
            'icon' => 'process-icon-back',
        ];

        parent::initContent();

        // Build messages HTML
        $messages = '';
        if (count($this->errors)) {
            $messages .= '<div class="alert alert-danger">';
            $messages .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            $messages .= '<ul class="list-unstyled">';
            foreach ($this->errors as $error) {
                $messages .= '<li>' . $error . '</li>';
            }
            $messages .= '</ul></div>';
            $this->errors = []; // Clear to prevent duplicate display
        }
        if (count($this->confirmations)) {
            $messages .= '<div class="alert alert-success">';
            $messages .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            $messages .= '<ul class="list-unstyled">';
            foreach ($this->confirmations as $confirmation) {
                $messages .= '<li>' . $confirmation . '</li>';
            }
            $messages .= '</ul></div>';
            $this->confirmations = []; // Clear to prevent duplicate display
        }

        $this->content = $messages . $this->renderForm();
        $this->context->smarty->assign('content', $this->content);
    }

    public function renderForm()
    {
        $token_id = $this->csrf->getTokenId();
        $token_value = $this->csrf->getToken();
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Send a test')
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => $token_id
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Phone number'),
                    'name' => 'sendsms_phone',
                    'size' => 40,
                    'required' => true
                ),
                array(
                    'type' => 'textarea',
                    'rows' => 7,
                    'label' => $this->module->l('Messsage'),
                    'name' => 'sendsms_message',
                    'required' => true,
                    'class' => 'ps_sendsms_content',
                    'desc' => $this->module->l('160 characters remaining')
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->module->l('Short url?'),
                    'name' => 'sendsms_url',
                    'required' => false,
                    'values' => array(
                        'query' => array(
                            array(
                                'url' => null,
                            )
                        ),
                        'id' => 'url',
                        'name' => 'url'
                    ),
                    'desc' => $this->module->l('Please use only urls that start with https:// or http://')
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->module->l('Add an unsubscribe link?'),
                    'name' => 'sendsms_gdpr',
                    'required' => false,
                    'values' => array(
                        'query' => array(
                            array(
                                'gdpr' => null,
                            )
                        ),
                        'id' => 'gdpr',
                        'name' => 'gdpr'
                    ),
                    'desc' => $this->module->l('You must specify {gdpr} key message. {gdpr} key will be replaced automaticaly with confirmation unique confirmation link. If {gdpr} key is not specified confirmation link will be placed at the end of message.')
                )
            ),
            'submit' => array(
                'title' => $this->module->l('Send'),
                'class' => 'btn btn-default'
            )
        );

        $this->fields_value[$token_id] = $token_value;


        Media::addJsDefL('sendsms_var_name', $this->module->l(' remaining characters'));

        $this->context->controller->addJS(
            Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/count.js'
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            if ($this->csrf->checkValid()) {
                $phone = (string)(Tools::getValue('sendsms_phone'));
                $message = (string)(Tools::getValue('sendsms_message'));
                $short = Tools::getValue('sendsms_url_') ? true : false;
                $gdpr = Tools::getValue('sendsms_gdpr_') ? true : false;

                // Validate phone
                if (empty($phone) || !Validate::isPhoneNumber($phone)) {
                    $this->errors[] = $this->module->l('The phone number is not valid');
                }

                // Validate message
                if (empty($message)) {
                    $this->errors[] = $this->module->l('Please enter a message');
                }

                // Send SMS if no errors
                if (empty($this->errors)) {
                    $this->module->sendSms($message, 'test', $phone, $short, $gdpr);
                    $this->confirmations[] = $this->module->l('The test SMS has been sent successfully');
                }
            } else {
                $this->errors[] = $this->module->l('Invalid CSRF token!');
            }
        }
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->module->l('Send a test SMS');
        parent::initPageHeaderToolbar();
        unset($this->toolbar_btn['new']);
    }
}
