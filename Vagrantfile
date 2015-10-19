# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    version = "php5-5.6"
    hostname = "php.box"
    locale = "en_GB.UTF.8"

    # Box
    config.vm.box = "ubuntu/trusty64"

    # Shared folders
    config.vm.synced_folder ".", "/dal"

    # Setup
    config.vm.provision :shell, :inline => "touch .hushlogin"
    config.vm.provision :shell, :inline => "hostnamectl set-hostname #{hostname} && locale-gen #{locale}"
    config.vm.provision :shell, :inline => "apt-get update --fix-missing"
    config.vm.provision :shell, :inline => "apt-get install -q -y g++ make git curl vim"

    # Lang
    config.vm.provision :shell, :inline => "add-apt-repository ppa:ondrej/#{version} && apt-get update"
    config.vm.provision :shell, :inline => "apt-get install -q -y php5-dev php5-cli php5-curl php5-xdebug php5-mysql"
    config.vm.provision :shell, :inline => "curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer"

    # Database
    config.vm.provision :shell, :inline => "sudo debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password password password'"
    config.vm.provision :shell, :inline => "sudo debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password_again password password'"
    config.vm.provision :shell, :inline => "sudo apt-get install mysql-server-5.6 -y"
    config.vm.provision :shell, :inline => "sudo /etc/init.d/mysql start"
    config.vm.provision :shell, :inline => "cd /dal && make install && make db-install"

end
