<?php

/*
   ----------------------------------------------------------------------
   FusionInventory
   Copyright (C) 2010-2011 by the FusionInventory Development Team.

   http://www.fusioninventory.org/   http://forge.fusioninventory.org/
   ----------------------------------------------------------------------

   LICENSE

   This file is part of FusionInventory.

   FusionInventory is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 2 of the License, or
   any later version.

   FusionInventory is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with FusionInventory.  If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------
   Original Author of file: David DURIEUX
   Co-authors of file:
   Purpose of file:
   ----------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   define('GLPI_ROOT', '../../..');
}

include (GLPI_ROOT."/inc/includes.php");

commonHeader($LANG['plugin_fusioninventory']['title'][0],$_SERVER["PHP_SELF"],"plugins","fusioninventory","wizard-start");

//PluginFusioninventoryMenu::displayMenu("mini");

$PluginFusioninventoryWizard = new PluginFusioninventoryWizard();

$a_button = array('name' => 'Suivant',
                  'link' => $CFG_GLPI['root_doc'].'/plugins/fusioninventory/front/wizard_inventorycomputeroptions.php');


$PluginFusioninventoryWizard->displayShowForm($a_button,
                                             $PluginFusioninventoryWizard->filInventoryComputer(),
                                             "PluginFusinvinventoryConfig");

commonFooter();

?>