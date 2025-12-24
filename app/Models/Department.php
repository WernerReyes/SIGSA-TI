<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $table = 'ost_department';
    protected $primaryKey = 'id';

    public function users() {
        return $this->hasMany(User::class, 'dept_id', 'id');
    }
}


//  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//   `pid` int(11) unsigned DEFAULT NULL,
//   `tpl_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `sla_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `schedule_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `email_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `autoresp_email_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `manager_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `flags` int(10) unsigned NOT NULL DEFAULT 0,
//   `name` varchar(128) NOT NULL DEFAULT '',
//   `signature` text NOT NULL,
//   `ispublic` tinyint(1) unsigned NOT NULL DEFAULT 1,
//   `group_membership` tinyint(1) NOT NULL DEFAULT 0,
//   `ticket_auto_response` tinyint(1) NOT NULL DEFAULT 1,
//   `message_auto_response` tinyint(1) NOT NULL DEFAULT 0,
//   `path` varchar(128) NOT NULL DEFAULT '/',
//   `updated` datetime NOT NULL,
//   `created` datetime NOT NULL,