# Discount Module Database Schema

## Users Table

The `users` table stores basic user information.

```sql
CREATE TABLE IF NOT EXISTS `mydb`.`users` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;
```

### Fields
- `id` (INT, Primary Key): A unique identifier for each user.
- `name` (VARCHAR(45)): The name of the user.
- `email` (VARCHAR(45)): The email address of the user.
- `created_at` (DATETIME): The timestamp when the user was created.
- `updated_at` (DATETIME): The timestamp when the user was last updated.

### Relationships(cardinality)
- One-to-many relationship with `members` (foreign key: `user_id`).
- One-to-many relationship with `bookings` (foreign key: `user_id`).

---

## Schedules Table

The `schedules` table stores details of available schedules.

```sql
CREATE TABLE IF NOT EXISTS `mydb`.`schedules` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `description` VARCHAR(45) NULL,
  `price` DECIMAL(10,2) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;
```

### Fields
- `id` (INT, Primary Key): A unique identifier for each schedule.
- `name` (VARCHAR(45)): The name of the schedule.
- `description` (VARCHAR(45)): A short description of the schedule.
- `price` (DECIMAL(10,2)): The price for the schedule.
- `created_at` (DATETIME): The timestamp when the schedule was created.
- `updated_at` (DATETIME): The timestamp when the schedule was last updated.

### Relationships(cardinality)
- One-to-many relationship with `booking_items` (foreign key: `schedule_id`).

---

## Discounts Table

The `discounts` table stores different discount types and values.

```sql
CREATE TABLE IF NOT EXISTS `mydb`.`discounts` (
  `id` INT NOT NULL,
  `discount_type` ENUM('fixed', 'percentage') NULL,
  `discount_value` INT NULL,
  `max_discount_amount` INT NULL,
  `user_left` INT NULL,
  `valid_until` DATE NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;
```

### Fields
- `id` (INT, Primary Key): A unique identifier for each discount.
- `discount_type` (ENUM): The type of discount, either fixed or percentage.
- `discount_value` (INT): The value of the discount. If percentage, this represents the discount percentage.
- `max_discount_amount` (INT): The maximum amount of discount allowed (only applies to percentage-based discounts).
- `user_left` (INT): The remaining number of times this discount can be used.
- `valid_until` (DATE): The expiration date of the discount.
- `created_at` (DATETIME): The timestamp when the discount was created.
- `updated_at` (DATETIME): The timestamp when the discount was last updated.

---

## Bookings Table

The `bookings` table stores information about bookings made by users.

```sql
CREATE TABLE IF NOT EXISTS `mydb`.`bookings` (
  `id` INT NOT NULL,
  `user_id` INT NULL,
  `for_member` TINYINT NULL DEFAULT 0,
  `booking_date` DATE NULL,
  `total_amount` DECIMAL(10,2) NULL,
  `discount` DECIMAL(10,2) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_id_idx` (`user_id` ASC) VISIBLE,
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `mydb`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;
```

### Fields
- `id` (INT, Primary Key): A unique identifier for each booking.
- `user_id` (INT, Foreign Key): The user ID who made the booking.
- `for_member` (TINYINT): Indicates if the booking is for a family member (1 for yes, 0 for no).
- `member_id` (INT, Nullable): If `for_member` is 1, this represents the family member's ID.
- `booking_date` (DATE): The date when the booking was made.
- `total_amount` (DECIMAL(10,2)): The total amount for the booking.
- `discount` (DECIMAL(10,2)): The discount applied to the booking.
- `created_at` (DATETIME): The timestamp when the booking was created.
- `updated_at` (DATETIME): The timestamp when the booking was last updated.

### Relationships(cardinality)
- Many-to-one relationship with `users` (foreign key: `user_id`).
- One-to-many relationship with `booking_items` (foreign key: `booking_id`).

---

## Booking Items Table

The `booking_items` table stores individual items related to a booking.

```sql
CREATE TABLE IF NOT EXISTS `mydb`.`booking_items` (
  `id` INT NOT NULL,
  `booking_id` INT NULL,
  `schedule_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_booking_id_idx` (`booking_id` ASC) VISIBLE,
  INDEX `fk_schedule_id_idx` (`schedule_id` ASC) VISIBLE,
  CONSTRAINT `fk_booking_id`
    FOREIGN KEY (`booking_id`)
    REFERENCES `mydb`.`bookings` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_schedule_id`
    FOREIGN KEY (`schedule_id`)
    REFERENCES `mydb`.`schedules` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;
```

### Fields
- `id` (INT, Primary Key): A unique identifier for each booking item.
- `booking_id` (INT, Foreign Key): The booking ID to which this item belongs.
- `schedule_id` (INT, Foreign Key): The schedule ID that the item relates to.

### Relationships(cardinality)
- Many-to-one relationship with `bookings` (foreign key: `booking_id`).
- Many-to-one relationship with `schedules` (foreign key: `schedule_id`).

---

## Relationships Overview

- **Users**: One-to-many relationship with `members` and `bookings`.
- **Bookings**: Many-to-one relationship with `users` and one-to-many relationship with `booking_items`.
- **Booking Items**: Many-to-one relationship with `bookings` and `schedules`.
- **Schedules**: One-to-many relationship with `booking_items`.
