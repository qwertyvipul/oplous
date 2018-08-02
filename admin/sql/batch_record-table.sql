--TABLE-4
create table batch_record (
	batch_id int,
	serial_no int,
	student_id int,
	status int(1) default 1,
	primary key(batch_id, serial_no),
	constraint fkey4_student_id foreign key (student_id) references students(student_id) on delete set null
);

alter table batch_record
add constraint fkey4_batch_id foreign key (batch_id) references batch_info(batch_id) on delete cascade;