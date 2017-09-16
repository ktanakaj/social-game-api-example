# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "centos/7"

  config.vm.network "private_network", type: "dhcp"
  config.vm.network "forwarded_port", guest: 80, host: 80, auto_correct: true

  config.vm.synced_folder ".", "/vagrant", type: "virtualbox"

  config.vm.provider "virtualbox" do |vb|
      vb.cpus = 1
      vb.memory = "1024"
  end

  config.vm.provision "ansible_local" do |ansible|
    ansible.playbook = "vagrant_conf/playbook.yml"
    ansible.provisioning_path = "/vagrant"
  end

  config.vm.provision "shell", run: "always" do |s|
    s.inline = "ip a show dev eth1"
  end
end
