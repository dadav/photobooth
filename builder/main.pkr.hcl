packer {
  required_plugins {
    arm-image = {
      version = ">= 0.2.5"
      source  = "github.com/solo-io/arm-image"
    }
  }
}

variable "target_image_size" {
    type = number
    default = 6*1024*1024*1024
}

source "arm-image" "raspbian" {
    iso_url = "https://downloads.raspberrypi.com/raspios_arm64/images/raspios_arm64-2024-03-15/2024-03-15-raspios-bookworm-arm64.img.xz"
    iso_checksum = "7e53a46aab92051d523d7283c080532bebb52ce86758629bf1951be9b4b0560f"
    target_image_size = "${var.target_image_size}"
}


build {
    sources = [
        "source.arm-image.raspbian"
    ]

    # copy files into the image
    provisioner "file" {
        source = "${path.root}/overlay"
        destination = "/tmp/"
    }

    # the configuration folder is structured so that copying it to / will
    # place the files in the correct location. so do just that.
    # among other things, this will setup the bridge between the wifi and ethernet interfaces.
    provisioner "shell" {
        inline = [
            "cp -r /tmp/overlay/* /",
            "rm -rf /tmp/overlay"
        ]
    }

    provisioner "shell" {
        inline = [
            "apt-get update",
            "apt-get upgrade -y",
            "wget -O install-photobooth.sh https://raw.githubusercontent.com/PhotoboothProject/photobooth/dev/install-photobooth.sh",
            "bash install-photobooth.sh -username='pi' -silent",
        ]
    }
}
