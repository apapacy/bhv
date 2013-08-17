# CREATE statement for products
CREATE TABLE products (
  kod bigint(20) NOT NULL auto_increment,
  name varchar(100) default NULL,
  search char(100) default NULL,
  PRIMARY KEY  (`kod`)
);


# CREATE statement for orders_details
CREATE TABLE orders_details (
  kod_order bigint(20) NOT NULL,
  kod_product bigint(20) NOT NULL,
  count decimal(10,0) default NULL,
  price decimal(20,4) default NULL,
  PRIMARY KEY  (kod_order, kod_product)
);


insert into products (search) values ("������");
insert into products (search) values ("���������");
insert into products (search) values ("�������");
insert into products (search) values ("����������");
insert into products (search) values ("�������");
insert into products (search) values ("����������");
insert into products (search) values ("����");
insert into products (search) values ("�������");
insert into products (search) values ("�������");
insert into products (search) values ("����");
insert into products (search) values ("����");
insert into products (search) values ("������");
insert into products (search) values ("��������");
insert into products (search) values ("��������");
insert into products (search) values ("��������");
insert into products (search) values ("���������");
insert into products (search) values ("�����������");
insert into products (search) values ("�����");
insert into products (search) values ("�����");
insert into products (search) values ("����");
insert into products (search) values ("������");
insert into products (search) values ("�������");
insert into products (search) values ("���������");
insert into products (search) values ("������");
insert into products (search) values ("����");
insert into products (search) values ("����");
insert into products (search) values ("��������");
insert into products (search) values ("�������");
insert into products (search) values ("������");
insert into products (search) values ("�����");
insert into products (search) values ("��������");
insert into products (search) values ("������");
insert into products (search) values ("�������");
insert into products (search) values ("�������");
insert into products (search) values ("����");
insert into products (search) values ("������");
insert into products (search) values ("�������");
insert into products (search) values ("������");
insert into products (search) values ("������");
insert into products (search) values ("�������");
insert into products (search) values ("�����");
insert into products (search) values ("�������");
insert into products (search) values ("��������");
insert into products (search) values ("�����");
insert into products (search) values ("��������");
insert into products (search) values ("�����");
insert into products (search) values ("����");

update products set name = CONCAT_WS(" ", "����� ", kod, "(", search, ")");


insert into orders_details values (1,1,11,111.11);
insert into orders_details values (1,2,22,2222.22);
insert into orders_details values (1,3,33,4.44);
insert into orders_details values (1,4,55,6.71);
insert into orders_details values (1,5,78,9.45);
insert into orders_details values (1,6,101,4.55);
insert into orders_details values (1,7,20,3.45);
insert into orders_details values (1,8,35,43.41);
insert into orders_details values (1,9,18,41.22);
insert into orders_details values (1,10,35,4.17);
insert into orders_details values (1,11,18,7.29);
insert into orders_details values (1,12,18,56.41);
insert into orders_details values (1,13,53,41.22);
insert into orders_details values (1,14,78,5.45);
insert into orders_details values (1,15,45,4.17);
insert into orders_details values (1,16,32,7.29);
insert into orders_details values (1,17,54,5.45);
insert into orders_details values (1,18,78,41.22);
insert into orders_details values (1,19,15,4.17);
insert into orders_details values (1,20,34,5.45);
insert into orders_details values (1,21,64,41.22);
insert into orders_details values (1,22,45,4.17);
insert into orders_details values (1,23,12,41.22);
insert into orders_details values (1,24,35,7.29);
