--TABLE-7
create table class_record(
	class_id int,
	student_id int,
	total int,
	present int,
	absent int,
	status int(1),
	primary key(class_id, student_id),
	constraint fkey7_class_id foreign key(class_id) references class_info(class_id) on delete cascade
);

alter table class_record add constraint fkey7_student_id foreign key(student_id) references students (student_id) on delete cascade;