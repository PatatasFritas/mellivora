<?php

require('../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

head('Add Users');
menu_management();

section_head('Add User');
form_start(Config::get('MELLIVORA_CONFIG_SITE_ADMIN_RELPATH') . 'actions/new_users');
form_input_text('Email');
form_input_text('Team Name');

echo '<div class="form-group">
      <label class="col-sm-2 control-label" for="password">Password</label>
      <div class="col-sm-8">
          <input type="password" id="password" name="password" class="form-control" placeholder="Password" />
      </div>
      <div class="col-sm-2">
          <input type="button" class="btn btn-primary" value="Random" onclick="$(\'#password\')[0].type=\'text\';$(\'#password\')[0].value=\''.generate_random_string(12).'\'" />
      </div>
    </div>
';

$opts = db_query_fetch_all('SELECT * FROM countries ORDER BY country_name ASC');
form_select($opts, 'Country', 'id', Null, 'country_name');

form_input_checkbox('Enabled', true);
form_input_checkbox('Competing', true);

form_hidden('action', 'new');

form_button_submit('Add user');
form_end();


section_head('Add Users');
form_start(Config::get('MELLIVORA_CONFIG_SITE_ADMIN_RELPATH') . 'actions/new_users');

form_textarea('Users CSV');

$opts = db_query_fetch_all('SELECT * FROM countries ORDER BY country_name ASC');
form_select($opts, 'Country', 'id', Null, 'country_name');

form_input_checkbox('Enabled', true);
form_input_checkbox('Competing', true);

message_inline_blue('CSV Format: email,team_name,password');

form_hidden('action', 'csv');

form_button_submit('Add users');
form_end();


foot();

