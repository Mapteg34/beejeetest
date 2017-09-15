CREATE TABLE users
(
  id serial NOT NULL,
  login character varying(32) NOT NULL,
  email character varying(256) NOT NULL,
  hash character varying(32) NOT NULL,
  is_admin boolean NOT NULL DEFAULT false,
  CONSTRAINT users_id_pkey PRIMARY KEY (id),
  CONSTRAINT users_email_uniq UNIQUE (email),
  CONSTRAINT users_login_uniq UNIQUE (login)
);

CREATE TABLE tasks
(
  id serial NOT NULL,
  created timestamp with time zone NOT NULL DEFAULT now(),
  user_id integer,
  text text NOT NULL,
  completed boolean NOT NULL DEFAULT false,
  image_path character varying(128) NOT NULL,
  CONSTRAINT tasks_id_pkey PRIMARY KEY (id),
  CONSTRAINT tasks_2_users FOREIGN KEY (user_id)
      REFERENCES users (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX fki_tasks_2_users
  ON tasks
  USING btree
  (user_id);

