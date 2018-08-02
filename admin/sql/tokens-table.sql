--TABLE-12
create table tokens(
	token_id int auto_increment,
	account_type int(1),
	user_id int,
	password varchar(128),
	token varchar(128) unique,
	date_time timestamp,
	primary key(token_id)
);