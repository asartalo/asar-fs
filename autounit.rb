# Taken from https://gist.github.com/287950

# Prereqs:
# * Ruby
# * gem install watchr

# Usage:
# copy autounit to php project directory
# run watchr autounit

def phpunit(param)
 system("./vendor/bin/phpunit --stop-on-failure #{param}")
end

def clearConsole
 puts "\e[H\e[2J"  #clear console
end


watch('tests/.*Test.php') do |md|
 clearConsole
 puts "Modified #{md[0]}\nRunning test...\n"
 phpunit(md[0])
end

watch('src/Asar/FileSystem/(.*)\.(.*)') do |md|   # runs tests/ClassTest* whenever src/Class.php is changed
 clearConsole
 puts "Modified #{md[0]}"
 testpath = 'tests/Asar/Tests/FileSystem/Unit/' + md[1].sub(/./) { |s| s.upcase } + 'Test.php'
 if (md[2] == 'php' and File.exist?(testpath))
    puts "Running #{testpath}...\n"
    phpunit(testpath)
 else
    puts "No unit test found for #{md[0]}\nRunning all tests...\n"
    phpunit('tests')
 end
end
