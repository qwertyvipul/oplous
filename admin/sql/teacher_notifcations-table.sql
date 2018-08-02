--TABLE-11
create table teacher_notifications(
	nid int auto_increment,
	teacher_id int,
	summary varchar(120),
	date_time timestamp,
	read_code int(1) default 0,
	primary key (nid),
	constraint fkey11_teacher_id foreign key(teacher_id) references teachers(teacher_id) on delete cascade
);