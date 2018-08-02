--TABLE-6
create table class_info (
	class_id int auto_increment,
	batch_id int,
	subject_id int,
	teacher_id int,
	class_type varchar(1),
	class_name varchar(20),
	primary key(class_id),
	constraint fkey6_batch_id foreign key(batch_id) references batch_info(batch_id) on delete cascade
);

alter table class_info add constraint fkey6_subject_id foreign key(subject_id) references subject_info (subject_id) on delete cascade;
alter table class_info add constraint fkey6_teacher_id foreign key(teacher_id) references teachers (teacher_id) on delete set null;