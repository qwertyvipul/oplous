--TABLE-10
create table student_notifications(
	nid int auto_increment,
	student_id int,
	summary varchar(120),
	date_time timestamp,
	read_code int(1) default 0,
	primary key (nid),
	constraint fkey10_student_id foreign key(student_id) references students(student_id) on delete cascade
);