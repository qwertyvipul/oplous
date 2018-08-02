--TABLE-13
create table teacher_support(
	support_id int auto_increment,
	teacher_id int,
	email_id varchar(60),
	support_title varchar(60),
	support_info varchar(255),
	support_response varchar(255),
	support_status int(1) default 0,
	date_time timestamp,
	primary key(support_id),
	constraint fkey13_teacher_id foreign key(teacher_id) references teachers(teacher_id)
);