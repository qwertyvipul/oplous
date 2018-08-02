--TABLE-5
create table subject_info (
	subject_id int auto_increment,
	subject_code varchar(8) unique,
	subject_name varchar(50),
	primary key(subject_id)
);