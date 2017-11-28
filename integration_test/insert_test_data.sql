-- Insert fake data for testing

--- User matching web/example-client/credentials.php
insert into users(email, secret) values ('example@blocked.org.uk', 'PAFBjZmUPp4d3XTcookscqndJwo2LlHAWmcg');

insert into isps(name, description, queue_name, show_results, admin_email, admin_name, isp_type, isp_status)
  values ('T-Mobile', 'T-Mobile', 'TMobile', 1, 'tmobileadmin@fake.domain', 'T-Mobile admin name', 'fixed', 'running');
insert into isps(name, description,  queue_name,  show_results,  admin_email,  admin_name,isp_type,isp_status)
  values (  'EE',  'EE',  'EE',  1,  'EEadmin@fake.domain',  'EE admin name',  'fixed',  'running');
insert into isps(name, description,  queue_name,  show_results, admin_email,  admin_name,  isp_type,  isp_status)
  values (  'VirginMobile',  'VirginMobile',  'VirginMobile',  1,  'VirginMobileAdmin@fake.domain',  'VirginMobile admin name',  'fixed',  'running');
