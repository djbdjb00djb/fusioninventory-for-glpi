<?php

/*
   ------------------------------------------------------------------------
   FusionInventory
   Copyright (C) 2010-2013 by the FusionInventory Development Team.

   http://www.fusioninventory.org/   http://forge.fusioninventory.org/
   ------------------------------------------------------------------------

   LICENSE

   This file is part of FusionInventory project.

   FusionInventory is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   FusionInventory is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with FusionInventory. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   FusionInventory
   @author    David Durieux
   @co-author Kevin Roy
   @copyright Copyright (c) 2010-2014 FusionInventory team
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      http://www.fusioninventory.org/
   @link      http://forge.fusioninventory.org/projects/fusioninventory-for-glpi/
   @since     2010

   ------------------------------------------------------------------------
 */

namespace Fusioninventory\View;


class PluginFusioninventoryViewTask {


   public function showList() {
      \Search::show('PluginFusioninventoryTask');
   }

   /**
    * Display form for task configuration
    *
    * @param $items_id integer ID of the task
    * @param $options array
    *
    * @return bool TRUE if form is ok
    *
    **/
   function showForm($id, $options=array()) {

      $pfTaskjob = new PluginFusioninventoryTaskjob();
      $pfTaskjobstate = new PluginFusioninventoryTaskjobstate();

      if ($id!='') {
         $this->getFromDB($id);
      } else {
         $this->getEmpty();
      }

      $options['colspan'] = 2;
      $this->showTabs($options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Name')."&nbsp;:</td>";
      echo "<td>";
      Html::autocompletionTextField ($this, "name", $this->fields["name"]);
      echo "</td>";

      $a_taskjob = $pfTaskjob->find("`plugin_fusioninventory_tasks_id`='".$id."'", "id");
      echo "<td>";
      echo __('Active')."&nbsp;:";
      echo "</td>";
      echo "<td>";
      if (count($a_taskjob) > 0) {
         Dropdown::showYesNo("is_active", $this->fields["is_active"]);
      }
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      $display_but = 0;
      if ($this->fields["is_active"]) {
         if ($id!='') {
            $forcerundisplay = 1;
            $a_taskjobs = $pfTaskjob->find("`plugin_fusioninventory_tasks_id`='".$id."'");
            foreach ($a_taskjobs as $data) {
               $statejob = $pfTaskjobstate->stateTaskjob($data['id'], '930', 'value');
               if ($statejob != '') {
                  $forcerundisplay = 0;
               }
            }
            if ($forcerundisplay == '1') {
               echo '<th colspan="2">';
               echo '<input name="forcestart" value="'.__('Force start', 'fusioninventory').'"
                  class="submit" type="submit">';
               echo '</th>';
               $display_but = 1;
            }
         }

         // * Manage reset / reinitialization
         $reset = 0;
         $a_taskjobs = $pfTaskjob->find("`plugin_fusioninventory_tasks_id`='".$id."'");
         foreach ($a_taskjobs as $data) {
            $statejob = $pfTaskjobstate->stateTaskjob($data['id'], '930', 'value');
            if ($statejob == '') {
               if ($data['execution_id'] != $this->fields['execution_id']) {
                  $reset = 1;
               }
            }
         }
         if ($reset == '1') {
            echo '<th colspan="2">';
            echo '<input name="reset" value="'.__('Reinitialization', 'fusioninventory').'"
               class="submit" type="submit">';
            echo '</th>';
            $display_but = 1;
         }
      }
      if ($display_but == '0') {
         echo "<td colspan='2'></td>";
      }

      echo "<td>".__('Scheduled date', 'fusioninventory')."&nbsp;:</td>";
      echo "<td>";
      if ($id) {
         Html::showDateTimeFormItem("date_scheduled", $this->fields["date_scheduled"], 1, FALSE);
      } else {
         Html::showDateTimeFormItem("date_scheduled", date("Y-m-d H:i:s"), 1);
      }
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td rowspan='2'>".__('Comments')."&nbsp;:</td>";
      echo "<td rowspan='2'>";
      echo "<textarea cols='39' rows='2' name='comment' >".$this->fields["comment"]."</textarea>";
      echo "</td>";
      echo "<td>".__('Communication type', 'fusioninventory')."&nbsp;:</td>";
      echo "<td>";
      $com = array();
      $com['push'] = __('Server contacts the agent (push)', 'fusioninventory');

      $com['pull'] = __('Agent contacts the server (pull)', 'fusioninventory');

      Dropdown::showFromArray("communication",
         $com,
         array('value'=>$this->fields["communication"]));
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Periodicity')."&nbsp;:</td>";
      echo "<td>";
      Dropdown::showNumber("periodicity_count", array(
         'value' => $this->fields['periodicity_count'],
         'min'   => 0,
         'max'   => 300)
      );
      $a_time = array();
      $a_time[] = "------";
      $a_time['minutes'] = __('Minute(s)', 'fusioninventory');

      $a_time['hours'] = ucfirst(__('hour(s)', 'fusioninventory'));

      $a_time['days'] = ucfirst(__('day(s)', 'fusioninventory'));

      $a_time['months'] = ucfirst(__('month(s)', 'fusioninventory'));

      Dropdown::showFromArray("periodicity_type",
         $a_time,
         array('value'=>$this->fields['periodicity_type']));
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td colspan='2'></td>";
      if (! $this->fields["is_advancedmode"]) {
         echo "<td>";
         echo __('Advanced mode', 'fusioninventory')."&nbsp;:";
         echo "</td>";
         echo "<td>";
         Dropdown::showYesNo("is_advancedmode", $this->fields["is_advancedmode"]);
         echo "</td>";
      }

      echo "</tr>";

      $this->showFormButtons($options);
      //      $this->addDivForTabs();

      $pfTaskjob = new PluginFusioninventoryTaskjob();
      $pfTaskjob->displayList($id);
      return TRUE;
   }

   public function menuTasksLogs() {
      global $CFG_GLPI;

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr class='tab_bg_1'>";

      $cell = 'td';
      if (strstr($_SERVER['PHP_SELF'], '/tasksummary.')) {
         $cell ='th';
      }
      echo "<".$cell." align='center' width='33%'>";
      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/fusioninventory/front/tasksummary.php'>".
         __('Task management', 'fusioninventory')." (".__('Summary').")</a>";
      echo "</".$cell.">";

      $cell = 'td';
      if (strstr($_SERVER['PHP_SELF'], '/task.')) {
         $cell ='th';
      }
      echo "<".$cell." align='center' width='33%'>";
      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/fusioninventory/front/task.php'>".
         __('Task management', 'fusioninventory')." (".__('Normal').")</a>";
      echo "</".$cell.">";

      $cell = 'td';
      if (strstr($_SERVER['PHP_SELF'], '/taskjoblog.')) {
         $cell ='th';
      }
      echo "<".$cell." align='center'>";
      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/fusioninventory/front/taskjoblog.php'>".
         __('Logs')."</a>";
      echo "</".$cell.">";
      echo "</tr>";
      echo "</table>";

   }

   function displayTask($condition) {
      global $DB, $CFG_GLPI;

      $pfTaskjob = new PluginFusioninventoryTaskjob();
      $pfTaskjoblog = new PluginFusioninventoryTaskjoblog();

      echo "<table class='tab_cadrehov'>";

      switch ($condition) {

         case 'next':
            $result = $this->getTasksPlanned();
            break;

         case 'running':
            $result = $this->getTasksRunning();
            break;

         case 'inerror':
            $result = $this->getTasksInerror();
            break;

         case 'actives':
            $query = "SELECT * FROM `glpi_plugin_fusioninventory_tasks`
               WHERE `is_active`='1' ".
               getEntitiesRestrictRequest("AND", 'glpi_plugin_fusioninventory_tasks');
            $result = $DB->query($query);
            break;

         case 'inactives':
            $query = "SELECT * FROM `glpi_plugin_fusioninventory_tasks`
               WHERE `is_active`='0' ".
               getEntitiesRestrictRequest("AND", 'glpi_plugin_fusioninventory_tasks');
            $result = $DB->query($query);
            break;

         case 'all':
            $query = "SELECT * FROM `glpi_plugin_fusioninventory_tasks`
               WHERE ".getEntitiesRestrictRequest("", 'glpi_plugin_fusioninventory_tasks');
            $result = $DB->query($query);
            break;

      }
      while ($data_task=$DB->fetch_array($result)) {
         $this->getFromDB($data_task['id']);
         Session::addToNavigateListItems($this->getType(), $data_task['id']);
         echo "<tr class='tab_bg_1'>";
         echo "<td width='32'>";
         $conditionpic = $condition;
         if ($this->fields['is_active'] == '0') {
            $conditionpic = 'inactives';
         } else if ($DB->numrows($this->getTasksPlanned($this->fields['id'])) > 0) {
            $conditionpic = 'next';
         } else if ($DB->numrows($this->getTasksRunning($this->fields['id'])) > 0){
            $conditionpic = 'running';
         }

         if ($conditionpic == 'next') {
            echo "<img src='".$CFG_GLPI['root_doc'].
               "/plugins/fusioninventory/pics/task_scheduled.png'/></td>";
         } else if ($conditionpic == 'inactives') {
            echo "<img src='".$CFG_GLPI['root_doc'].
               "/plugins/fusioninventory/pics/task_disabled.png'/></td>";
         } else if ($conditionpic == 'actives') {
            echo "<img src='".$CFG_GLPI['root_doc'].
               "/plugins/fusioninventory/pics/task_enabled.png'/></td>";
         } else if ($conditionpic == 'running') {
            echo "<img src='".$CFG_GLPI['root_doc'].
               "/plugins/fusioninventory/pics/task_running.png'/></td>";
         } else {

         }
         echo "<td>
            <a href='".$this->getFormURL()."?id=".$data_task['id']."' style='font-size: 16px; '>"
            .$this->getName()."</a> (".ucfirst($data_task['communication'])." ";
         if ($data_task['communication'] == "push") {
            Html::showToolTip(__('Server contacts the agent (push)', 'fusioninventory'));

         } else if ($data_task['communication'] == "pull") {
            Html::showToolTip(__('Agent contacts the server (pull)', 'fusioninventory'));

         }
         echo ")<br/>&nbsp;";
         $a_taskjobs = $pfTaskjob->find("`plugin_fusioninventory_tasks_id`='".$data_task['id']."'");
         if (count($a_taskjobs) > 1) {
            foreach ($a_taskjobs as $data_taskjob) {
               $pfTaskjob->getFromDB($data_taskjob['id']);
               echo "| ".$pfTaskjob->getLink(1)." ";
            }
            if (count($a_taskjobs) > 0) {
               echo "|";
            }
         }
         echo "</td>";

         echo "<td>".__('Next run')." : <br/>".$data_task['date_scheduled']."</td>";

         $queryt = "SELECT * FROM `glpi_plugin_fusioninventory_taskjobstates`
            LEFT JOIN `glpi_plugin_fusioninventory_taskjobs`
            ON `plugin_fusioninventory_taskjobs_id`=`glpi_plugin_fusioninventory_taskjobs`.`id`
            WHERE `plugin_fusioninventory_tasks_id`='".$data_task['id']."'
            ORDER BY `uniqid`
            LIMIT 1";
$resultt = $DB->query($queryt);
if ($DB->numrows($resultt) != 0) {
   $datat = $DB->fetch_assoc($resultt);
   $pfTaskjoblog->displayShortLogs($datat['plugin_fusioninventory_taskjobs_id'], 1);
} else {
   echo "<td>".__('Last run')." :<br/>
      ".__('Never')."</td>";
}

echo "</tr>";
      }

      echo "</table>";
   }

   //function taskMenu() {
   //   global $DB;
   //
   //   $resultTasksPlanned = $this->getTasksPlanned();
   //   $resultTasksRunning = $this->getTasksRunning();
   //   $resultTasksInerror = $this->getTasksInerror();
   //   $a_tasksActives = $this->find("`is_active` = '1' ".
   //           getEntitiesRestrictRequest("AND", 'glpi_plugin_fusioninventory_tasks'));
   //   $a_tasksInactives = $this->find("`is_active` = '0' ".
   //           getEntitiesRestrictRequest("AND", 'glpi_plugin_fusioninventory_tasks'));
   //   $a_tasksAll = $this->find(getEntitiesRestrictRequest("",
   //                                                        'glpi_plugin_fusioninventory_tasks'));
   //
   //
   //   if (!isset($_GET['see'])) {
   //      if ($DB->numrows($resultTasksPlanned) > 0) {
   //         $_GET['see'] = 'next';
   //      } else if ($DB->numrows($resultTasksRunning) > 0) {
   //         $_GET['see'] = 'running';
   //      } else if ($DB->numrows($resultTasksInerror) > 0) {
   //         $_GET['see'] = 'inerror';
   //      } else if (count($a_tasksActives) > 0) {
   //         $_GET['see'] = 'actives';
   //      } else {
   //         $_GET['see'] = 'all';
   //      }
   //   }
   //
   //   Session::initNavigateListItems($this->getType());
   //
   //   //The following $_GET assignment code seems unneeded since it doesn't
   //   // use Search class to show the tasks list
   //   unset($_GET['field']);
   //   unset($_GET['searchtype']);
   //   unset($_GET['contains']);
   //   unset($_GET['itemtype']);
   //   $_GET['reset'] = 'reset';
   //   if ($_GET['see'] == 'actives') {
   //      $_GET['field'] = array('5');
   //      $_GET['searchtype'] = array('equals');
   //      $_GET['contains'] = array('1');
   //      $_GET['itemtype'] = array('PluginFusioninventoryTask');
   //   } else if ($_GET['see'] == 'inactives') {
   //      $_GET['field'] = array('5');
   //      $_GET['searchtype'] = array('equals');
   //      $_GET['contains'] = array('0');
   //      $_GET['itemtype'] = array('PluginFusioninventoryTask');
   //   }
   //
   //   //The taskMenu method doesn't use the preceding search parameters and
   //   // it kills the search form in "Normal Task" list display
   //   //Search::manageGetValues($this->getType());
   //
   //   echo "<table class='tab_cadre_fixe'>";
   //   echo "<tr class='tab_bg_1'>";
   //
   //   // ** Get task in next execution
   //   $cell = 'td';
   //   if ($_GET['see'] == 'next') {
   //      $cell = 'th';
   //   }
   //   echo "<".$cell." align='center'><a href='".$_SERVER['PHP_SELF']."?see=next'>".
   //           __('Planned for running', 'fusioninventory')."<sup>(".
   //           $DB->numrows($resultTasksPlanned).")</sup></a></".$cell.">";
   //
   //   // ** Get task running
   //   $cell = 'td';
   //   if ($_GET['see'] == 'running') {
   //      $cell = 'th';
   //   }
   //   echo "<".$cell." align='center'><a href='".$_SERVER['PHP_SELF']."?see=running'>".
   //           __('Running')."<sup>(".
   //           $DB->numrows($resultTasksRunning).")</sup></a></".$cell.">";
   //
   //   // ** Get task in error
   //   $cell = 'td';
   //   if ($_GET['see'] == 'inerror') {
   //      $cell = 'th';
   //   }
   //   echo "<".$cell." align='center'><a href='".$_SERVER['PHP_SELF']."?see=inerror'>".
   //           __('In error', 'fusioninventory')."<sup>(".
   //           $DB->numrows($resultTasksInerror).")</sup></a></".$cell.">";
   //
   //   // ** Get task active
   //   $cell = 'td';
   //   if ($_GET['see'] == 'actives') {
   //      $cell = 'th';
   //   }
   //   echo "<".$cell." align='center'><a href='".$_SERVER['PHP_SELF']."?see=actives'>".
   //           __('Active')."<sup>(".
   //           count($a_tasksActives).")</sup></a></".$cell.">";
   //
   //   // ** Get task inactive
   //   $cell = 'td';
   //   if ($_GET['see'] == 'inactives') {
   //      $cell = 'th';
   //   }
   //   echo "<".$cell." align='center'><a href='".$_SERVER['PHP_SELF']."?see=inactives'>".
   //           __('Inactive')."<sup>(".
   //           count($a_tasksInactives).")</sup></a></".$cell.">";
   //
   //   // ** Get all task
   //   $cell = 'td';
   //   if ($_GET['see'] == 'all') {
   //      $cell = 'th';
   //   }
   //   echo "<".$cell." align='center'><a href='".$_SERVER['PHP_SELF']."?see=all'>".
   //           __('All')."<sup>(".
   //           count($a_tasksAll).")</sup></a></".$cell.">";
   //
   //   echo "</tr>";
   //   echo "</table>";
   //
   //
   //   echo "<div class='center' id='searchform' style='display:none'>";
   //
   //   echo "</div>";
   //}

}
