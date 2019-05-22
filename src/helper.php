<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_contacts_single
 *
 * @author     Sebastian Brümmer <sebastian@produktivbuero.de>
 * @copyright  Copyright (C) 2019 *produktivbüro . All rights reserved
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\String\StringHelper;

$com_path = JPATH_SITE . '/components/com_contact/';

JLoader::register('ContactHelperRoute', $com_path . 'helpers/route.php');
JModelLegacy::addIncludePath($com_path . 'models');

/**
 * Helper for mod_contacts_single
 *
 * @since  0.9.0
 */
abstract class modContactsSingleHelper
{
  /**
   * Get a list of contacts from a specific category
   *
   * @param   \Joomla\Registry\Registry  &$params  object holding the models parameters
   *
   * @return  mixed
   *
   * @since  0.9.0
   */
  public static function getList(&$params)
  {
    $ids = $params->get('ids', array());

    // fast fail
    if ( empty($ids) ) return array();
    
    // Get an instance of the generic articles model
    $contacts = JModelLegacy::getInstance('Category', 'ContactModel', array('ignore_request' => true));

    // Set application parameters in model
    $app       = JFactory::getApplication();
    $appParams = $app->getParams();
    $contacts->setState('params', $appParams);

    // Set basic filters
    $contacts->setState('filter.published', 1);
    $contacts->setState('filter.publish_date', true);
    $contacts->setState('filter.language', true);

    // Access filter (do not remove due to routing)
    $access     = !JComponentHelper::getParams('com_content')->get('show_noauth');
    $contacts->setState('filter.access', $access);

    // Set ordering
    $ordering = $params->get('ordering', 'ordering');
    $contacts->setState('list.ordering', $ordering);

    $direction = $ordering == 'rand()' ? '' : $params->get('direction', 'ASC');
    $contacts->setState('list.direction', $direction);

    $all = $contacts->getItems();

    // FIlter selected items and add routing
    $items =  array();
    foreach ($all as $single) {
      if ( !in_array($single->id, $ids) ) continue;

      // Add routing
      $single->slug = $single->id . ':' . $single->alias;
      
      // Find menu itemid
      $value = ContactHelperRoute::getCategoryRoute($single->catid);
      $menuItem = $app->getMenu()->getItems( 'link', $value, true );
      $itemId = empty($menuItem) ? 0 : $menuItem->id;

      // We know that we have access (see "Access filter")
      $single->link = JRoute::_( ContactHelperRoute::getContactRoute($single->slug, $single->catid).'&Itemid='.$itemId );

      $items[] = $single;
    }

    return $items;
  }


  /**
   * Get a symbol or text of contact information
   *
   * @param   int     $symbol  symbol (0) or text (1)
   * @param   string  $type  type of symbol
   *
   * @return  string
   *
   * @since  0.9.0
   */
  public static function getSymbol($symbol, $type)
  {
    $return = '';

    // load language files
    JFactory::getLanguage()->load('mod_contacts_single', JPATH_SITE.'/modules/mod_contacts_single');

    switch ($type)
    {
      case 'email':
          $return = $symbol == 0 ? '<span class="icon-mail"></span> ' : JText::_('MOD_CONTACTS_SINGLE_EMAIL');
        break;

      case 'telephone':
          $return = $symbol == 0 ? '<span class="icon-phone"></span> ' : JText::_('MOD_CONTACTS_SINGLE_TELEPHONE');
        break;

      case 'mobile':
          $return = $symbol == 0 ? '<span class="icon-mobile"></span> ' : JText::_('MOD_CONTACTS_SINGLE_MOBILE');
        break;

      case 'fax':
          $return = $symbol == 0 ? '<span class="icon-print"></span> ' : JText::_('MOD_CONTACTS_SINGLE_FAX');
        break;

      case 'webpage':
          $return = $symbol == 0 ? '<span class="icon-new-tab"></span> ' : JText::_('MOD_CONTACTS_SINGLE_WEBPAGE');
        break;
    }

    return $return;
  }
}
