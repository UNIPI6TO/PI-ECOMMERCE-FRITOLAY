terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/mysql"
  }
  required_providers {
    google = { source = "hashicorp/google", version = "~> 5.0" }
  }
}
provider "google" { project = "project-3e1faa58-1e7d-4e8d-933", region = "us-central1" }

data "google_project" "project" {}

data "google_kms_crypto_key" "mysql_key" {
  name = "projects/${data.google_project.project.project_id}/locations/us-central1/keyRings/fritolay-keyring/cryptoKeys/mysql-key"
}

resource "google_sql_database_instance" "mysql_instance" {
  name             = "fritolay-mysql"
  database_version = "MYSQL_8_0"
  region           = "us-central1"

  settings {
    tier      = "db-f1-micro"
    disk_type = "PD_HDD"
    disk_size = 10
    
    ip_configuration {
      ipv4_enabled = true
    }
  }
  encryption_key_name = data.google_kms_crypto_key.mysql_key.id
  deletion_protection = false
}

resource "google_sql_database" "database" {
  name     = "fritolay_db"
  instance = google_sql_database_instance.mysql_instance.name
}
