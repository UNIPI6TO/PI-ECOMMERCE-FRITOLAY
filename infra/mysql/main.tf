terraform {
  backend "gcs" {
    bucket = "tfstate-pi-8vo-semestre-2026"
    prefix = "terraform/state/mysql"
  }
  required_providers {
    google = { source = "hashicorp/google", version = "~> 5.0" }
  }
}
provider "google" {
  project = "project-3e1faa58-1e7d-4e8d-933"
  region  = "us-central1"
}

data "google_project" "project" {}

data "google_kms_crypto_key" "mysql_key" {
  name     = "mysql-key"
  key_ring = "projects/${data.google_project.project.project_id}/locations/us-central1/keyRings/fritolay-keyring"
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

    database_flags {
      name  = "cloudsql_iam_authentication"
      value = "on"
    }
  }
  encryption_key_name = data.google_kms_crypto_key.mysql_key.id
  deletion_protection = false
}

resource "google_sql_database" "database" {
  name     = "fritolay_db"
  instance = google_sql_database_instance.mysql_instance.name
}

resource "google_sql_user" "backend_sa_user" {
  name     = "sa-backend@project-3e1faa58-1e7d-4e8d-933.iam"
  instance = google_sql_database_instance.mysql_instance.name
  type     = "CLOUD_IAM_SERVICE_ACCOUNT"
}

resource "google_sql_user" "admin_user" {
  name     = "octavosemetreuniandes2026@gmail.com"
  instance = google_sql_database_instance.mysql_instance.name
  type     = "CLOUD_IAM_USER"
}

resource "google_project_iam_member" "admin_sql_client" {
  project = data.google_project.project.project_id
  role    = "roles/cloudsql.client"
  member  = "user:octavosemetreuniandes2026@gmail.com"
}

resource "google_project_iam_member" "admin_sql_instance_user" {
  project = data.google_project.project.project_id
  role    = "roles/cloudsql.instanceUser"
  member  = "user:octavosemetreuniandes2026@gmail.com"
}

resource "google_project_iam_member" "admin_sql_admin" {
  project = data.google_project.project.project_id
  role    = "roles/cloudsql.admin"
  member  = "user:octavosemetreuniandes2026@gmail.com"
}
