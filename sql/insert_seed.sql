.separator ,
.import 'seed/users.csv' users
.import 'seed/tags.csv' tags
UPDATE tags SET image_filename = NULL WHERE image_filename = '';
.import 'seed/tag_incompatible_ralations.csv' tag_incompatible_ralations
.import 'seed/clohtes.csv' clohtes
UPDATE clohtes SET image_filename = NULL WHERE image_filename = '';
.import 'seed/clothes_tags.csv' clothes_tags
.import 'seed/laundries.csv' laundries
.import 'seed/laundry_clothes.csv' laundry_clothes
