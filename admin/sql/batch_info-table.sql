--TABLE-3
create table batch_info (
	batch_id int auto_increment,
	batch_code varchar(8),
	parent_id int default null,
	year int(1),
	primary key(batch_id)
);