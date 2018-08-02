--TABLE-8
create table atd_info(
	atd_id int auto_increment,
	class_id int,
	count int,
	date_time timestamp,
	primary key(atd_id),
	constraint fkey8_class_id foreign key(class_id) references class_info(class_id) on delete cascade
);