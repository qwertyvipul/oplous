--TABLE-9
create table atd_log(
	atd_id int,
	student_id int,
	status int,
	date_time timestamp,
	primary key(atd_id, student_id),
	constraint fkey9_atd_id foreign key(atd_id) references atd_info(atd_id) on delete cascade
);

alter table atd_log add constraint fkey9_student_id foreign key(student_id) references students(student_id) on delete cascade;