<?php

function boss_on_qihuo_manual_uploaded($data, $db) {

	$db->exec(
"insert into front.diamond_info_all
select d.* from front.diamond_info d left join front.diamond_info_all a on d.cert_id = a.cert_id
where a.cert_id is null;
update front.diamond_info_all a inner join front.diamond_info i on i.cert_id = a.cert_id
set a.good_type = i.good_type
where a.good_type <> i.good_type; ");
	echo 'finish copying!!!'.PHP_EOL;
}


?>