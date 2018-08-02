--TABLE-14
create table student_support(
	support_id int auto_increment,
	student_id int,
	email_id varchar(60),
	support_title varchar(60),
	support_info varchar(255),
	support_response varchar(255),
	support_status int(1) default 0,
	date_time timestamp,
	primary key(support_id),
	constraint fkey14_student_id foreign key(student_id) references students(student_id)
);