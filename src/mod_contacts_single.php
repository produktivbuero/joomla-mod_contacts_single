<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_contacts_single
 *
 * @author     Sebastian Brümmer <sebastian@produktivbuero.de>
 * @copyright  Copyright (C) 2019 *produktivbüro . All rights reserved
 * @license    GNU General Public License version 2 or later
 */

use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

// Include the class of the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
$list = modContactsSingleHelper::getList($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require ModuleHelper::getLayoutPath('mod_contacts_single', $params->get('layout', 'default'));
