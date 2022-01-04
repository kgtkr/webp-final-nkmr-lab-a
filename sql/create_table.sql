CREATE TABLE users (
  id TEXT NOT NULL PRIMARY KEY,
  hashed_password TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL
);

CREATE INDEX idx_users_created_at ON users (created_at);

CREATE TABLE tags (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  image_sha256 TEXT,
  created_at TIMESTAMPTZ NOT NULL,
  user_id TEXT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE INDEX idx_tags_created_at ON tags (created_at);

CREATE TABLE tag_incompatible_ralations (
  tag_id1 INTEGER NOT NULL,
  tag_id2 INTEGER NOT NULL,
  FOREIGN KEY (tag_id1) REFERENCES tags (id),
  FOREIGN KEY (tag_id2) REFERENCES tags (id),
  PRIMARY KEY (tag_id1, tag_id2),
  CHECK (tag_id1 < tag_id2)
);

CREATE INDEX idx_tag_incompatible_ralations_tag_id1 ON tag_incompatible_ralations (tag_id1);
CREATE INDEX idx_tag_incompatible_ralations_tag_id2 ON tag_incompatible_ralations (tag_id2);

CREATE TABLE clohtes (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  image_sha256 TEXT,
  created_at TIMESTAMPTZ NOT NULL,
  user_id TEXT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE INDEX idx_clohtes_created_at ON clohtes (created_at);

CREATE TABLE clothes_tags (
  tag_id INTEGER NOT NULL,
  clothes_id INTEGER NOT NULL,
  FOREIGN KEY (tag_id) REFERENCES tags (id),
  FOREIGN KEY (clothes_id) REFERENCES clothes (id),
  PRIMARY KEY (tag_id, clothes_id)
);

CREATE INDEX idx_clothes_tags_tag_id ON clothes_tags (tag_id);
CREATE INDEX idx_clothes_tags_clothes_id ON clothes_tags (clothes_id);

CREATE TABLE laundries (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  created_at TIMESTAMPTZ NOT NULL,
  user_id TEXT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE INDEX idx_laundries_created_at ON laundries (created_at);

CREATE TABLE laundry_clothes (
  laundry_id INTEGER NOT NULL,
  clothes_id INTEGER NOT NULL,
  FOREIGN KEY (laundry_id) REFERENCES laundries (id),
  FOREIGN KEY (clothes_id) REFERENCES clothes (id),
  PRIMARY KEY (laundry_id, clothes_id)
);

CREATE INDEX idx_laundry_clothes_laundry_id ON laundry_clothes (laundry_id);
CREATE INDEX idx_laundry_clothes_clothes_id ON laundry_clothes (clothes_id);
