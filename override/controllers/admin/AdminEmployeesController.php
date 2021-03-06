<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http:* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http:*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http:*  International Registered Trademark & Property of PrestaShop SA
*/
class AdminEmployeesController extends AdminEmployeesControllerCore
{
    public function __construct()
    {
        parent::__construct();
        $this->fields_list = array(
            'id_employee' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'firstname' => array('title' => $this->l('First Name')),
            'lastname' => array('title' => $this->l('Last Name')),
            'email' => array('title' => $this->l('Email address')),
            'keyyo_caller' => array('title' => $this->l('Compte Keyyo')),
            'keyyo_notification_enabled' => array('title' => $this->l('Notification d\'appels')),
            'keyyo_notification_numbers' =>array('title' => $this->l('Numéros notifiés')),
            'profile' => array('title' => $this->l('Profile'), 'type' => 'select', 'list' => $this->profiles_array,
                'filter_key' => 'pl!name', 'class' => 'fixed-width-lg'),
            'active' => array('title' => $this->l('Active'), 'align' => 'center', 'active' => 'status',
                'type' => 'bool', 'class' => 'fixed-width-sm'),
        );
    }

    public function renderForm()
    {

        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $available_profiles = Profile::getProfiles($this->context->language->id);
        if ($obj->id_profile == _PS_ADMIN_PROFILE_ && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->errors[] = Tools::displayError('You cannot edit the SuperAdmin profile.');
            return AdminController::renderForm();
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Employees'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'class' => 'fixed-width-xl',
                    'label' => $this->l('First Name'),
                    'name' => 'firstname',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'class' => 'fixed-width-xl',
                    'label' => $this->l('Last Name'),
                    'name' => 'lastname',
                    'required' => true
                ),
                array(
                    'type' => 'html',
                    'name' => 'employee_avatar',
                    'html_content' => '',
                ),
                array(
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->l('Email address'),
                    'name' => 'email',
                    'required' => true,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'prefix' => '<i class="icon-phone"></i>',
                    'label' => $this->l('Compte KEYYO employé'),
                    'name' => 'keyyo_caller',
                    'hint' => $this->l('Votre numéro au format international'),
                    'required' => false,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'prefix' => '<i class="icon-phone"></i>',
                    'label' => $this->l('Numéros KEYYO des notifications'),
                    'name' => 'keyyo_notification_numbers',
                    'hint' => $this->l('Les numéros au format international, séparé par une virgule'),
                    'required' => false,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Notifications d\'appels KEYYO'),
                    'name' => 'keyyo_notification_enabled',
                    'required' => false,
                    'is_Bool' => true,
                    'values' => array(
                        array(
                            'id' => 'keyyo_notification_enabled_on',
                            'value' => 1,
                            'label' => $this->l('Oui')
                        ),
                        array(
                            'id' => 'keyyo_notification_enabled_off',
                            'value' => 0,
                            'label' => $this->l('Non')
                        )
                    )
                ),
            ),
        );
        if ($this->restrict_edition) {
            $this->fields_form['input'][] = array(
                'type' => 'change-password',
                'label' => $this->l('Password'),
                'name' => 'passwd'
            );
            if (Tab::checkTabRights(Tab::getIdFromClassName('AdminModulesController'))) {
                $this->fields_form['input'][] = array(
                    'type' => 'prestashop_addons',
                    'label' => 'PrestaShop Addons',
                    'name' => 'prestashop_addons',
                );
            }
        } else {
            $this->fields_form['input'][] = array(
                'type' => 'password',
                'label' => $this->l('Password'),
                'hint' => sprintf($this->l('Password should be at least %s characters long.'), Validate::ADMIN_PASSWORD_LENGTH),
                'name' => 'passwd'
            );
        }
        $this->fields_form['input'] = array_merge($this->fields_form['input'], array(
            array(
                'type' => 'switch',
                'label' => $this->l('Subscribe to PrestaShop newsletter'),
                'name' => 'optin',
                'required' => false,
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'optin_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'optin_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
                'hint' => $this->l('PrestaShop can provide you with guidance on a regular basis by sending you tips on how to optimize the management of your store which will help you grow your business. If you do not wish to receive these tips, you can disable this option.')
            ),
            array(
                'type' => 'default_tab',
                'label' => $this->l('Default page'),
                'name' => 'default_tab',
                'hint' => $this->l('This page will be displayed just after login.'),
                'options' => $this->tabs_list
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Language'),
                'name' => 'id_lang',
                'options' => array(
                    'query' => Language::getLanguages(false),
                    'id' => 'id_lang',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Theme'),
                'name' => 'bo_theme_css',
                'options' => array(
                    'query' => $this->themes,
                    'id' => 'id',
                    'name' => 'name'
                ),
                'onchange' => 'var value_array = $(this).val().split("|"); $("link").first().attr("href", "themes/" + value_array[0] + "/css/" + value_array[1]);',
                'hint' => $this->l('Back office theme.')
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Admin menu orientation'),
                'name' => 'bo_menu',
                'required' => false,
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'bo_menu_on',
                        'value' => 0,
                        'label' => $this->l('Top')
                    ),
                    array(
                        'id' => 'bo_menu_off',
                        'value' => 1,
                        'label' => $this->l('Left')
                    )
                )
            )
        ));
        if ((int)$this->tabAccess['edit'] && !$this->restrict_edition) {
            $this->fields_form['input'][] = array(
                'type' => 'switch',
                'label' => $this->l('Active'),
                'name' => 'active',
                'required' => false,
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                    )
                ),
                'hint' => $this->l('Allow or disallow this employee to log into the Admin panel.')
            );
            if ($this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
                foreach ($available_profiles as $i => $profile) {
                    if ($available_profiles[$i]['id_profile'] == _PS_ADMIN_PROFILE_) {
                        unset($available_profiles[$i]);
                        break;
                    }
                }
            }
            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Permission profile'),
                'name' => 'id_profile',
                'required' => true,
                'options' => array(
                    'query' => $available_profiles,
                    'id' => 'id_profile',
                    'name' => 'name',
                    'default' => array(
                        'value' => '',
                        'label' => $this->l('-- Choose --')
                    )
                )
            );
            if (Shop::isFeatureActive()) {
                $this->context->smarty->assign('_PS_ADMIN_PROFILE_', (int)_PS_ADMIN_PROFILE_);
                $this->fields_form['input'][] = array(
                    'type' => 'shop',
                    'label' => $this->l('Shop association'),
                    'hint' => $this->l('Select the shops the employee is allowed to access.'),
                    'name' => 'checkBoxShopAsso',
                );
            }
        }
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        $this->fields_value['passwd'] = false;
        $this->fields_value['bo_theme_css'] = $obj->bo_theme . '|' . $obj->bo_css;
        if (empty($obj->id)) {
            $this->fields_value['id_lang'] = $this->context->language->id;
        }
        return AdminController::renderForm();
    }
}
