--TABLE-2
create table teachers (
	teacher_id int auto_increment,
    name varchar(50),
    email_id varchar(60) unique,
    password varchar(128),
    primary key(teacher_id)
);