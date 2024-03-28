#!/bin/bash

mkdir -p uploaded

grep -o '[^ ]*\.ico' logs/upload.log > output.txt

# Read the output.txt file line by line
while IFS= read -r line; do
  # Remove the 'Put ' and ' successfully!' parts from the line
  filename=$(echo "$line" | sed -e 's/Put //' -e 's/ successfully!//')

  # Check if the file exists in the output directory
  if [ -f "output/$filename" ]; then
    # Move the file to the uploaded directory
    mv "output/$filename" uploaded/
    echo "Moved $filename to uploaded directory."
  else
    echo "File $filename not found in output directory."
  fi
done < output.txt