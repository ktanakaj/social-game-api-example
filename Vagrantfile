# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "centos/7"

  # ホストPCとゲストPCのネットワークを構築
  config.vm.network "private_network", type: "dhcp"
  # ゲストPCのポートをホストPCに転送
  config.vm.network "forwarded_port", guest: 80, host: 80, auto_correct: true
  config.vm.network "forwarded_port", guest: 3306, host: 3306, auto_correct: true

  # ホストPCのこのフォルダをマウント
  config.vm.synced_folder ".", "/vagrant", type: "nfs"

  # VM環境設定
  config.vm.provider "hyperv" do |vb, override|
    vb.cpus = 2
    vb.memory = "2048"
    override.vm.synced_folder ".", "/vagrant", type: "smb", mount_options: ["dir_mode=0777,file_mode=0777"]
  end
  config.vm.provider "virtualbox" do |vb|
    vb.cpus = 2
    vb.memory = "2048"
  end

  # ゲストPCにansibleをインストールし共有フォルダのプレイブックを実行
  config.vm.provision "ansible_local" do |ansible|
    ansible.playbook = "ansible/playbook.yml"
    ansible.provisioning_path = "/vagrant/"
    ansible.raw_arguments = ['--force-handlers']
  end

  # DHCPで割り当てられたIPアドレスを表示
  config.vm.provision "shell", run: "always" do |s|
    s.inline = "ip addr"
  end
end
