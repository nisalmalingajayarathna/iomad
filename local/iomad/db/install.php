<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// This script is run after the dashboard has been installed.

function xmldb_local_iomad_install() {
    global $CFG, $DB;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    // Create new Company Manager role.
    if (!$companymanager = $DB->get_record( 'role', array( 'shortname' => 'companymanager') )) {
        $companymanagerid = create_role( 'Company Manager', 'companymanager',
        '(Iomad) Manages individual companies - can upload users etc.');
    } else {
        $companymanagerid = $companymanager->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $companymanagerid );
    if (empty($levels)) {
        $level = new stdClass;
        $level->roleid = $companymanagerid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Company Department Manager role.
    if (!$companydepartmentmanager = $DB->get_record('role',
                                     array( 'shortname' => 'companydepartmentmanager'))) {
        $companydepartmentmanagerid = create_role('Company Department Manager',
        'companydepartmentmanager',
        'Iomad Manages departments within companies - can upload users etc.');
    } else {
        $companydepartmentmanagerid = $companydepartmentmanager->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $companydepartmentmanagerid );
    if (empty($levels)) {
        $level = new stdclass;
        $level->roleid = $companydepartmentmanagerid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Client Administrator role.
    if (!$clientadministrator = $DB->get_record('role',
                                                 array('shortname' => 'clientadministrator'))) {
        $clientadministratorid = create_role( 'Client Administrator', 'clientadministrator',
        '(Iomad) Manages site - can create new companies and add managers etc.');
    } else {
        $clientadministratorid = $clientadministrator->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $clientadministratorid );
    if (empty($levels)) {
        $level = new stdclass();
        $level->roleid = $clientadministratorid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Client Course Editor role.
    if (!$companycourseeditor = $DB->get_record('role',
                                                 array('shortname' => 'companycourseeditor'))) {
        $companycourseeditorid = create_role( 'Client Course Editor', 'companycourseeditor',
        'Iomad Client Course Editor - can edit course content; add, delete, modify etc..');
    } else {
        $companycourseeditorid = $companycourseeditor->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $companycourseeditorid );
    if (empty($levels)) {
        $level = new stdclass;
        $level->roleid = $companycourseeditorid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Client Course Access role.
    if (!$companycoursenoneditor = $DB->get_record('role',
                                                   array('shortname' => 'companycoursenoneditor'))) {
        $companycoursenoneditorid = create_role('Client Course Access', 'companycoursenoneditor',
        'Iomad Client Course Access - similar to the non-editing teacher role for client admin');
    } else {
        $companycoursenoneditorid = $companycoursenoneditor->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels($clientadministratorid);
    if (empty($levels)) {
        $level = null;
        $level->roleid = $clientadministratorid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Add capabilities to above.
    $clientadministratorcaps = array(
        'local/iomad_dashboard:view',
        'block/iomad_company_admin:assign_company_manager',
        'block/iomad_company_admin:assign_department_manager',
        'block/iomad_company_admin:company_add',
        'block/iomad_company_admin:company_course_users',
        'block/iomad_company_admin:company_delete',
        'block/iomad_company_admin:company_edit',
        'block/iomad_company_admin:company_manager',
        'block/iomad_company_admin:company_user',
        'block/iomad_company_admin:createcourse',
        'block/iomad_company_admin:user_create',
        'block/iomad_company_admin:user_upload',
        'block/iomad_company_admin:view',
        'block/iomad_link:view',

        'block/iomad_online_users:viewlist',
        'block/iomad_reports:view',
        'coursereport/iomad_completion:view',
        'coursereport/iomad_companies:view'
    );
    foreach ($clientadministratorcaps as $cap) {
        assign_capability( $cap, CAP_ALLOW, $clientadministratorid, $systemcontext->id);
    }
    $companydepartmentmanagercaps = array('block/iomad_report:view',
        'local/iomad_dashboard:view',
        'block/iomad_online_users:viewlist',
        'block/iomad_link:view',
        'block/iomad_company_admin:view_licenses',
        'block/iomad_company_admin:view',
        'block/iomad_company_admin:user_upload',
        'block/iomad_company_admin:user_create',
        'block/iomad_company_admin:editusers',
        'block/iomad_company_admin:edit_departments',
        'block/iomad_company_admin:company_view',
        'block/iomad_company_admin:company_course_users',
        'block/iomad_company_admin:assign_department_manager',
        'block/iomad_company_admin:allocate_licenses'
    );

    foreach ($companydepartmentmanagercaps as $cap) {
        assign_capability( $cap, CAP_ALLOW, $companydepartmentmanagerid, $systemcontext->id );
    }

    $companymanagercaps = array(
        'block/iomad_online_users:viewlist',
        'block/iomad_link:view',
        'block/iomad_company_admin:view_licenses',
        'block/iomad_company_admin:view',
        'block/iomad_company_admin:user_upload',
        'block/iomad_company_admin:user_create',
        'block/iomad_company_admin:editusers',
        'block/iomad_company_admin:edit_departments',
        'block/iomad_company_admin:company_view',
        'block/iomad_company_admin:company_course_users',
        'block/iomad_company_admin:assign_department_manager',
        'block/iomad_company_admin:allocate_licenses',
        'block/iomad_company_admin:assign_company_manager',
        'block/iomad_company_admin:classrooms',
        'block/iomad_company_admin:classrooms_delete',
        'block/iomad_company_admin:classrooms_edit',
        'block/iomad_company_admin:company_edit',
        'block/iomad_company_admin:company_course_unenrol',
        'block/iomad_company_admin:company_manager',
        'block/iomad_company_admin:company_user_profiles',
        'block/iomad_company_admin:createcourse'

    );

    $companycoursenoneditorcaps = array('block/side_bar_block:viewblock',
        'gradereport/grader:view',
        'gradereport/user:view',
        'mod/assignment:view',
        'mod/book:read',
        'mod/certificate:manage',
        'mod/certificate:view',
        'mod/choice:readresponses',
        'mod/feedback:view',
        'mod/forum:addquestion',
        'mod/forum:createattachment',
        'mod/forum:deleteownpost',
        'mod/forum:replypost',
        'mod/forum:startdiscussion',
        'mod/forum:viewdiscussion',
        'mod/forum:viewqandawithoutposting',
        'mod/page:view',
        'mod/quiz:attempt',
        'mod/quiz:view',
        'mod/resource:view',
        'mod/survey:participate',
        'moodle/block:view',
        'moodle/grade:viewall',
        'moodle/site:viewfullnames',
        'moodle/site:viewuseridentity');

    $companycourseeditorcaps = array('block/side_bar_block:editblock',
        'block/side_bar_block:viewblock',
        'booktool/importhtml:import',
        'booktool/print:print',
        'enrol/authorize:manage',
        'enrol/license:manage',
        'enrol/license:unenrol',
        'enrol/manual:enrol',
        'enrol/manual:unenrol',
        'gradereport/grader:view',
        'gradereport/overview:view',
        'gradereport/user:view',
        'mod/assignment:exportownsubmission',
        'mod/assignment:grade',
        'mod/assignment:view',
        'mod/book:edit',
        'mod/book:read',
        'mod/book:viewhiddenchapters',
        'mod/certificate:manage',
        'mod/certificate:view',
        'mod/choice:choose',
        'mod/choice:deleteresponses',
        'mod/choice:downloadresponses',
        'mod/choice:readresponses',
        'mod/courseclassroom:grade',
        'mod/courseclassroom:invite',
        'mod/courseclassroom:viewattendees',
        'mod/forum:addnews',
        'mod/forum:addquestion',
        'mod/forum:createattachment',
        'mod/forum:deleteanypost',
        'mod/forum:deleteownpost',
        'mod/forum:editanypost',
        'mod/forum:exportdiscussion',
        'mod/forum:exportownpost',
        'mod/forum:exportpost',
        'mod/forum:managesubscriptions',
        'mod/forum:movediscussions',
        'mod/forum:postwithoutthrottling',
        'mod/forum:rate',
        'mod/forum:replynews',
        'mod/forum:replypost',
        'mod/forum:splitdiscussions',
        'mod/forum:startdiscussion',
        'mod/forum:viewallratings',
        'mod/forum:viewanyrating',
        'mod/forum:viewdiscussion',
        'mod/forum:viewhiddentimedposts',
        'mod/forum:viewqandawithoutposting',
        'mod/forum:viewrating',
        'mod/forum:viewsubscribers',
        'mod/page:view',
        'mod/resource:view',
        'mod/scorm:deleteresponses',
        'mod/scorm:savetrack',
        'mod/scorm:viewreport',
        'mod/scorm:viewscores',
        'mod/url:view',
        'moodle/block:edit',
        'moodle/block:view',
        'moodle/calendar:manageentries',
        'moodle/calendar:managegroupentries',
        'moodle/calendar:manageownentries',
        'moodle/course:activityvisibility',
        'moodle/course:changefullname',
        'moodle/course:changesummary',
        'moodle/course:manageactivities',
        'moodle/course:managefiles',
        'moodle/course:managegroups',
        'moodle/course:markcomplete',
        'moodle/course:reset',
        'moodle/course:sectionvisibility',
        'moodle/course:setcurrentsection',
        'moodle/course:update',
        'moodle/course:viewhiddenactivities',
        'moodle/course:viewhiddensections',
        'moodle/course:viewparticipants',
        'moodle/grade:edit',
        'moodle/grade:hide',
        'moodle/grade:lock',
        'moodle/grade:manage',
        'moodle/grade:managegradingforms',
        'moodle/grade:manageletters',
        'moodle/grade:manageoutcomes',
        'moodle/grade:unlock',
        'moodle/grade:viewall',
        'moodle/grade:viewhidden',
        'moodle/notes:manage',
        'moodle/notes:view',
        'moodle/rating:rate',
        'moodle/rating:view',
        'moodle/rating:viewall',
        'moodle/rating:viewany',
        'moodle/role:switchroles',
        'moodle/site:accessallgroups',
        'moodle/site:manageblocks',
        'moodle/site:trustcontent',
        'moodle/site:viewfullnames',
        'moodle/site:viewreports',
        'moodle/site:viewuseridentity',
        'moodle/user:viewdetails',
        'report/courseoverview:view',
        'report/log:view',
        'report/log:viewtoday',
        'report/loglive:view',
        'report/outline:view',
        'report/participation:view',
        'report/progress:view');

    foreach ($companymanagercaps as $cap) {
        assign_capability( $cap, CAP_ALLOW, $companymanagerid, $systemcontext->id );
    }

    foreach ($companycourseeditorcaps as $rolecapability) {
        // Assign_capability will update rather than insert if capability exists.
        assign_capability($rolecapability, CAP_ALLOW, $companycourseeditorid,
                          $systemcontext->id);
    }

    foreach ($companycoursenoneditorcaps as $rolecapability) {
        // Assign_capability will update rather than insert if capability exists.
        assign_capability($rolecapability, CAP_ALLOW, $companycoursenoneditorid,
                          $systemcontext->id);
    }

    // Create custom user field for company (if not already existing).
    if (!$DB->get_record('user_info_field', array('shortname' => 'company'))) {
        $field = new stdClass;
        $field->shortname = 'company';
        $field->name = 'Company';
        $field->datatype = 'text';
        $field->description = 'User Company';
        $field->descriptionformat = 1;
        $field->categoryid = 0;
        $field->required = 0;
        $field->locked = 1;
        $field->visible = 0;
        $field->param1 = 30;
        $field->param2 = 2048;
        $DB->insert_record('user_info_field', $field);
    }

    // Even worse - change the theme.
    $theme = theme_config::load('iomad');
    set_config('theme', $theme->name);

    // Enable completion tracking.
    set_config('enablecompletion', 1);

    // Set the default blocks in courses.
    $defblocks = ':iomad_link,iomad_company_selector,iomad_online_users,completionstatus';
    set_config('defaultblocks_topics', $defblocks);
    set_config('defaultblocks_weeks', $defblocks);

}