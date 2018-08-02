--TABLE-1
create table students (
	student_id int,
    name varchar(50),
    email_id varchar(60) unique,
    password varchar(128),
    primary key(student_id)
);