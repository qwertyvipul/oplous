--ALL TABLES --14
create table students (
	student_id int,
    name varchar(50),
    email_id varchar(60) unique,
    password varchar(128),
    primary key(student_id)
);

create table teachers (
	teacher_id int auto_increment,
    name varchar(50),
    email_id varchar(60) unique,
    password varchar(128),
    primary key(teacher_id)
);

create table batch_info (
	batch_id int auto_increment,
	batch_code varchar(8),
	parent_id int default null,
	year int(1),
	primary key(batch_id)
);

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

create table subject_info (
	subject_id int auto_increment,
	subject_code varchar(8) unique,
	subject_name varchar(50),
	primary key(subject_id)
);

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

create table atd_info(
	atd_id int auto_increment,
	class_id int,
	count int,
	date_time timestamp,
	primary key(atd_id),
	constraint fkey8_class_id foreign key(class_id) references class_info(class_id) on delete cascade
);

create table atd_log(
	atd_id int,
	student_id int,
	status int,
	date_time timestamp,
	primary key(atd_id, student_id),
	constraint fkey9_atd_id foreign key(atd_id) references atd_info(atd_id) on delete cascade
);

alter table atd_log add constraint fkey9_student_id foreign key(student_id) references students(student_id) on delete cascade;

create table teacher_notifications(
	nid int auto_increment,
	teacher_id int,
	summary varchar(120),
	date_time timestamp,
	read_code int(1) default 0,
	primary key (nid),
	constraint fkey11_teacher_id foreign key(teacher_id) references teachers(teacher_id) on delete cascade
);

create table student_notifications(
	nid int auto_increment,
	student_id int,
	summary varchar(120),
	date_time timestamp,
	read_code int(1) default 0,
	primary key (nid),
	constraint fkey10_student_id foreign key(student_id) references students(student_id) on delete cascade
);

create table tokens(
	token_id int auto_increment,
	account_type int(1),
	user_id int,
	password varchar(128),
	token varchar(128) unique,
	date_time timestamp,
	primary key(token_id)
);

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